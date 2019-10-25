<?php
namespace AppBundle\Controller\Administration;

use AppBundle\Entity\Tournament;
use AppBundle\Form\AddRankingType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;
use AppBundle\Form\TournamentRankType;
use AppBundle\Form\RankingFieldType;
use Doctrine\ORM\Query\Expr;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use AppBundle\Entity\TournamentRanking;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use AppBundle\Entity\TournamentRanking\TournamentRankingManager;
use AppBundle\Entity\BlindLevel;
use AppBundle\Entity\TournamentManager;

/**
 */
class AdministrationController extends Controller 
{

	/**
	 *  Auswahl live event
	 *
	 * @Route("/live/{tournamentId}", name="administration_live")
	 * @Template("AppBundle:Administration:live.html.twig")
	 */
	public function liveAction(Request $request, $tournamentId = null) 
	{
		/** @var $tournamentRankingManager TournamentRankingManager **/
		$tournamentRankingManager = $this->get('bpn.tournament_ranking.manager');
		/** @var $tournamentManager TournamentManager **/
		$tournamentManager = $this->get('bpn.tournament.manager');
		
		if(!empty($tournamentId))
		{
			/** @var Tournament $tournament */
		    $tournament = $tournamentManager->find($tournamentId);
		    // FORM live_config
    		$currentBlind = $tournament->getBlindLevel();
		    $formLiveConfig = $this->createFormBuilder($tournament)
		          ->add('blindLevel', EntityType::class, array(
		                  'class' => BlindLevel::class,
		                  'choice_label' => 'name',
		                  'empty_data' => null,
		                  'required' => false,
		                  'placeholder' => 'Blind Level auswählen',
		                  'query_builder' => function (EntityRepository $er) use ($currentBlind) {
    		                  return $er->createQueryBuilder('b')
    		                      // ->where('b.level >= :currLevel')
    		                      // ->setParameter('currLevel', empty($currentBlind) ? 0 : $currentBlind->getLevel())
    		                      ->orderBy('b.level', 'ASC');
		                  },
		          ))
				->add('submit', SubmitType::class, array('label' => 'Speichern'))
				->getForm();
			
				
			// FORM edit_ranking
			$playersAlive = $tournament->getPlayers();
			$formEditRank = $this->createFormBuilder()
    			->add('player', EntityType::class, array(
    			        'label' => 'Spieler',
    			        'class' => 'AppBundle:Player',
    			        'choices' => $playersAlive,
    			        'placeholder' => 'Spieler wählen',
    			        'required' => true
    			))
    			->add('kickedByPlayer', EntityType::class, array(
    			        'label' => 'ausgeschieden gegen',
    			        'class' => 'AppBundle:Player',
    			        'choices' => $playersAlive,
    			        'placeholder' => 'Spieler wählen',
    			        'required' => false,
    			        'empty_data' => null
    			))
    			->add('kickedAt', DateTimeType::class, array(
    			        'label' => 'Zeitpunk',
    			        'widget' => 'single_text',
    			        'format' => 'dd.MM.yyyy HH:mm:ss',
    			        'data' => new \DateTime(),
    			        'required' => true
    			))
			 ->add('submit', SubmitType::class, array('label' => 'Update'))
		     ->getForm();

		     
		    $formLiveConfig->handleRequest($request);
		    if($formLiveConfig->isSubmitted() && $formLiveConfig->isValid())
		    {
				if($tournament->getBlindLevel() == null)
				{
					$tournament->setLastBlindRaiseAt(null);
				}
				else
				{
					$tournament->setLastBlindRaiseAt(new \DateTime());
				}
		        $tournamentManager->update($tournament);
		        $this->addFlash('success', 'Blind Level aktualisiert.');
		        return $this->redirectToRoute('administration_live', array('tournamentId' => $tournamentId));
		    }
		
    	     $formEditRank->handleRequest($request);
    	     if($formEditRank->isSubmitted() && $formEditRank->isValid())
    	     {
    	         $player = $formEditRank->get('player')->getData();
    	         $kickedBy = $formEditRank->get('kickedByPlayer')->getData();
    	         $kickedAt = $formEditRank->get('kickedAt')->getData();
    	         
    	         /** @var $ranking TournamentRanking **/
    	         $ranking = $tournamentRankingManager->findOneByUnique($tournament, $player);
    	         
    	         if(empty($ranking))
    	         {
    	             $this->addFlash('error', "Spieler $player ist kein Turnierteilnehmer!");
    	         }
    	         else
    	         {
    	             $ranking
    	               ->setKickedByPlayer($kickedBy)
    	               ->setKickedAt($kickedAt);
    	             
	                 if($tournamentRankingManager->update($ranking))
	                 {
	                     $this->addFlash('success', 'Platzierung erfolgreich angepasst!');
	                 }
	                 else 
	                 {
	                     $this->addFlash('error', 'Platzierung konnte nicht angepasst werden!');
	                 }
    	         }
    	         
    	         return $this->redirectToRoute('administration_live', array('tournamentId' => $tournamentId));
    	     }
				
			return array (
				'tournament' => $tournament,
		        'frm_edit_rank' => $formEditRank->createView(),
				'frm_live_config' => $formLiveConfig->createView()
			);
		}
		
		// select tournament
		$formSelectEvent = $this->createFormBuilder()
			->add('tournament', EntityType::class, array(
					'label' => 'Turnier wählen',
					'class' => 'AppBundle:Tournament',
			        'placeholder' => 'Bitte Turnier wählen',
			        'query_builder' => function (EntityRepository $er) {
			        return $er->createQueryBuilder('t')
			         ->orderBy('t.date', 'DESC');
			        },
			        'group_by' => 'event'
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
}
