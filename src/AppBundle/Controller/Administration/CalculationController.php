<?php
namespace AppBundle\Controller\Administration;

use AppBundle\Entity\Tournament;
use AppBundle\Entity\TournamentRanking;
use AppBundle\Form\AddRankingType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use AppBundle\Entity\AnnualRanking\AnnualRanking;
use AppBundle\Entity\AnnualRanking\AnnualRankingManager;
use AppBundle\Entity\TournamentManager;
use Doctrine\Common\Util\Debug;
use AppBundle\Entity\PlayerManager;
use AppBundle\Entity\Player;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @Route("/calculation")
 */
class CalculationController extends Controller 
{
	
	/**
	 * Acion für Berechnung
	 *
	 * @Route("/", name="admin_calculation_index")
	 * @Template("AppBundle:Administration:calculation.html.twig")
	 */
	public function calculationIndexAction(Request $request) 
	{
	    /** @var AnnualRankingManager $annualRankingManager **/
	    $annualRankingManager = $this->get('bpn.annual_ranking.manager');
	    
		$formStart = $this->createFormBuilder()
			->add('submit', SubmitType::class, array('label' => 'Jetzt neu berechnen'))
			->getForm();
		$formStart->handleRequest($request);
		
		if($formStart->isValid())
		{
			$this->createAnnualRanking();
		}

		$listAnnualRanking = $annualRankingManager->findCurrentRanking();
		return array (
			'frm_start' => $formStart->createView(),
			'list_annualRanking' => $listAnnualRanking
		);
	}
	
	private function createAnnualRanking()
	{
	    /** @var AnnualRankingManager $annualRankingManager **/
	    $annualRankingManager = $this->get('bpn.annual_ranking.manager');
	    /** @var TournamentManager $tournamentManager **/
	    $tournamentManager = $this->get('bpn.tournament.manager');
	    /** @var PlayerManager $playerManager **/
	    $playerManager = $this->get('bpn.player.manager');
           
	    // Lösche bisheriges Ranking
	    $annualRankingManager->removeAll();

	    $listTournaments = array_reverse($tournamentManager->findPastMainTournaments());
	    
	    $startIndex = (\count($listTournaments)) % 5;
        $listPlayer = $playerManager->findAll();
	    
	    $sumPoints = array();
	    
	    /** @var Tournament $tournament **/
	    foreach ($listTournaments as $key => $tournament)
	    {
	        $ranking = $tournament->getRanking()->toArray();
	        $ranking = array_combine(array_map(function($item){ return $item->getPlayer()->getId(); }, $ranking), $ranking);
	        
	        $tournamentPlayers = new ArrayCollection(array_map(function($item){ return $item->getPlayer();}, $ranking));
	        $sumPlayers = \count($ranking);
	        /** @var Player $player **/
	        foreach ($listPlayer as $player)
	        {
	            $points = 0;
	          
	            // hat teilgenommen
	            if($tournamentPlayers->contains($player))
	            {
	                $playerRanking = $ranking[$player->getId()]->getRank();
	                
	                $points = $sumPlayers - $playerRanking + 1;	               
	            }	                
	            
	            // werte setzen
                if(!array_key_exists($player->getId(), $sumPoints))
                    $sumPoints[$player->getId()] = 0;
	            
	            // summe zurücksetzen
	            if($startIndex == $key || (($startIndex + 5) == $key))
	            {
                    $sumPoints[$player->getId()] = $points;
	            }
	            // addieren
	            else 
	            {    
                    $sumPoints[$player->getId()] += $points;	                
	            }
                
                $newRanking = $annualRankingManager->create($player, $tournament)
                   ->setPoints($points)
                   ->setSumPoints($sumPoints[$player->getId()]);
                
                $annualRankingManager->update($newRanking);
	        }
	    }
	   return true;
	    
	}
	
	private function createAnnualRankingOld($tournaments)
	{
		$em = $this->getDoctrine()->getManager();
		/* @var $playerRepo PlayerRepository */
		$playerRepo = $em->getRepository('AppBundle:Player');
		$tournamentRankingRepo = $em->getRepository('AppBundle:TournamentRanking');

		/** @var $manager AnnualRankingManager **/
		$annualManager = $this->get('bpn.annual_ranking.manager');
		
		// Lösche bisheriges Ranking
		foreach($annualManager->findAll() as $element)
		    $em->remove($element);
		    
		    $em->flush();
		    
		// Zähle Spieler pro Turnier
		$tournamentCount = array();
		$tournaments = array_reverse($tournaments);
		foreach($tournaments as $tournament)
		{
			$tournamentCount[$tournament->getId()] = \count($tournament->getRanking());
		}
		
		// BERECHNUNG
		
		// alle Spieler
		$listPlayer = $playerRepo->findAll();
		foreach ($listPlayer as $player)
		{
			$playerSumPkt = 0;
			// Durchlaufe alle relevanten Turniere
			/* @var $tournament Tournament */
			foreach($tournaments as $tournament)
			{
				$tournamentRank = $tournamentRankingRepo->findOneBy(array('player' => $player, 'tournament' => $tournament));
				$annualRanking = $annualRepo->findOneBy(array('player' => $player, 'tournament' => $tournament));
				if(empty($annualRanking))
				{
					$annualRanking = $annualManager->create($player, $tournament);
					$annualManager->update($annualRanking, false);
				}
				$points = 0;
				if(!empty($tournamentRank))
				{
					$rank = $tournamentRank->getRank();
					$points = $tournamentCount[$tournament->getId()] - $rank + 1;
					$playerSumPkt += $points;
				}
				
				$annualRanking
				    ->setPoints($points)
				    ->setSumPoints($playerSumPkt);
			}
		}
		$em->flush();
		return true;
	}
}
