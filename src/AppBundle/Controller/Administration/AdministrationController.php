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

/**
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
	 *  Auswahl live event
	 *
	 * @Route("/live/{tournamentId}", name="administration_live")
	 * @Template("AppBundle:Administration:live.html.twig")
	 */
	public function liveAction(Request $request, $tournamentId = null) 
	{
		$em = $this->getDoctrine()->getManager();
		/** @var $tournamentRankingManager TournamentRankingManager **/
		$tournamentRankingManager = $this->get('bpn.tournament_ranking.manager');
		
		if(!empty($tournamentId))
		{
		    $tournament = $em->getRepository('AppBundle:Tournament')->find($tournamentId);
		    $formLiveConfig = $this->createFormBuilder()
				->add('submit', SubmitType::class, array('label' => 'Speichern'))
				->getForm();
			
				
				
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
    			        'required' => true
    			))
    			->add('kickedAt', DateTimeType::class, array(
    			        'label' => 'Zeitpunk',
    			        'widget' => 'single_text',
    			        'format' => 'dd.MM.yyyy HH:mm',
    			        'data' => new \DateTime(),
    			        'required' => true
    			))
			 ->add('submit', SubmitType::class, array('label' => 'Update'))
		     ->getForm();
			
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
