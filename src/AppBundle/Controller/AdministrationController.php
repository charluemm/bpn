<?php
namespace AppBundle\Controller;

use AppBundle\Entity\AnnualRanking;
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

/**
 * @Route("/administration")
 */
class AdministrationController extends Controller 
{
	/**
	 * Action für Auslosung
	 *
	 * @Route("/draw", name="administration_draw")
	 * @Template("AppBundle:Administration:draw.html.twig")
	 */
	public function drawAction() 
	{
		$em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('AppBundle:Player')->findAll();
        
		return array (
			'listPlayer' => $entities
		);
	}

	/**
	 * @Route("/live/{tournamentId}/update-ranking", name="administration_live_ranking_update")
	 * @Template("AppBundle:Administration:live.html.twig")
	 * @Method("PUT")
	 */
	public function updateRankingAction(Request $request, $tournamentId)
	{
		$em = $this->getDoctrine()->getManager();
		$entity = $em->getRepository('AppBundle:Tournament')->find($tournamentId);
		
		if (!$entity) {
			throw $this->createNotFoundException('Unable to find Tournament entity.');
		}
		
		$editForm = $this->createForm(AddRankingType::class, $entity, array(
						'method' => 'PUT',
				))
				->add('submit', SubmitType::class, array('label' => 'Update'));
			
		$editForm->handleRequest($request);
		if ($editForm->isValid())
		{
			$em = $this->getDoctrine()->getManager();
			$em->flush();
		
			return $this->redirect($this->generateUrl('administration_live', array('tournamentId' => $tournamentId)));
		}
		
		return array(
				'entity'      => $entity,
				'edit_form'   => $editForm->createView(),
		);
		
	}
	
	/**
	 *  Auswahl live event
	 *
	 * @Route("/live/{tournamentId}", name="administration_live")
	 * @Template("AppBundle:Administration:live.html.twig")
	 */
	public function liveAction(Request $request, $tournamentId = null) 
	{
		$em = $this->getDoctrine()->getManager();

		if(!empty($tournamentId))
		{
			$tournament = $em->getRepository('AppBundle:Tournament')->find($tournamentId);
			$formLiveConfig = $this->createFormBuilder()
				->add('submit', SubmitType::class, array('label' => 'Speichern'))
				->getForm();
			
			$formLiveRanking = $this->createForm(AddRankingType::class, $tournament, array(
						'action' => $this->generateUrl('administration_live_ranking_update', array('tournamentId' => $tournamentId)),
						'method' => 'PUT',
				))
				->add('submit', SubmitType::class, array('label' => 'Update'));
			
			return array (
				'tournament' => $tournament,
				'frm_live_ranking' => $formLiveRanking->createView(),
				'frm_live_config' => $formLiveConfig->createView()
			);
		}
		
		// select tournament
		$formSelectEvent = $this->createFormBuilder()
			->add('tournament', EntityType::class, array(
					'label' => 'Turnier wählen',
					'class' => 'AppBundle:Tournament',
			))
			->add('submit', SubmitType::class, array('label' => 'Laden'))
			->getForm();
			
		$formSelectEvent->handleRequest($request);
		if($formSelectEvent->isValid())
		{
			$tournamentId = $formSelectEvent->get('tournament')->getData()->getId();
			
			return $this->redirect($this->generateUrl('administration_live', array(
						'tournamentId' => $tournamentId,
			)));
		}
        
		return array (
			'frm_select_tournament' => $formSelectEvent->createView()
		);
	}

	/**
	 * Acion für Auslosung
	 *
	 * @Route("/calculation", name="calculation_index")
	 * @Template("AppBundle:Administration:calculation.html.twig")
	 */
	public function calculationIndexAction(Request $request) 
	{
		$em = $this->getDoctrine()->getManager();
        $listTournaments = $em->getRepository('AppBundle:Tournament')->findAnnualRankingRelevant();
		
		$formStart = $this->createFormBuilder()
			->add('submit', SubmitType::class, array('label' => 'Jetzt neu berechnen'))
			->getForm();
		$formStart->handleRequest($request);
		if($formStart->isValid())
		{
			$this->createAnnualRanking($listTournaments);
		}

		$listAnnualRanking = $em->getRepository('AppBundle:AnnualRanking')->findLastGroupByPlayer();
		return array (
			'frm_start' => $formStart->createView(),
			'list_tournaments' => $listTournaments,
			'list_annualRanking' => $listAnnualRanking
		);
	}
	
	private function createAnnualRanking($tournaments)
	{
		$em = $this->getDoctrine()->getManager();
		/* @var $playerRepo PlayerRepository */
		$playerRepo = $em->getRepository('AppBundle:Player');
		$tournamentRankingRepo = $em->getRepository('AppBundle:TournamentRanking');
		$annualRepo = $em->getRepository('AppBundle:AnnualRanking');

		// Lösche bisheriges Ranking
		foreach($annualRepo->findAll() as $element)
			$em->remove($element);
			
		$em->flush();
		// Zähle Spieler pro Turnier
		$tournamentCount = array();
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
				$date = $tournament->getDate();
				$tournamentRank = $tournamentRankingRepo->findOneBy(array('player' => $player, 'tournament' => $tournament));
				$annualRanking = $annualRepo->findOneBy(array('player' => $player, 'date' => $date));
				if(empty($annualRanking))
				{
					$annualRanking = new AnnualRanking($player);
					$em->persist($annualRanking);
				}
				$points = 0;
				if(!empty($tournamentRank))
				{
					$rank = $tournamentRank->getRank();
					$points = $tournamentCount[$tournament->getId()] - $rank + 1;
					$playerSumPkt += $points;
				}
				
				$annualRanking->setPoints($points)->setSumPoints($playerSumPkt)->setDate($date);
			}
		}
		$em->flush();
		return true;
	}
}
