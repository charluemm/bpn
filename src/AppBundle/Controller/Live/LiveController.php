<?php

namespace AppBundle\Controller\Live;

use AppBundle\Entity\Tournament;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityRepository;

/**
 * @author Michael Müller <development@reu-network.de>
 *
 */
class LiveController extends Controller
{
    /**
     * @Route("/{tournamentId}", name="live_index")
     * @Template("AppBundle:Live:live.html.twig")
     */
    public function showAction(Request $request, $tournamentId = null)
    {
    	$em = $this->getDoctrine()->getManager();
    	    	
    	if(!empty($tournamentId))
    	{
    		/* @var $tournament Tournament */
    		$tournament = $em->getRepository('AppBundle:Tournament')->find($tournamentId);
    	
    		return array(
    				'tournament' => $tournament
    		);    		
    	}
    	else
    	{
	    	$frmSelectTournament = $this->createFormBuilder()
	    		->add('tournament', EntityType::class, array(
	    				'label' => 'Turnier wählen',
	    		        'placeholder' => 'Turnier für Live-Ansicht wählen',
	    				'class' => 'AppBundle:Tournament',
	    		        'query_builder' => function (EntityRepository $er) {
            		        return $er->createQueryBuilder('t')
            		        ->orderBy('t.date', 'DESC');
	    		        },
	    		        'group_by' => 'event'
	    		))
	    		->add('submit', SubmitType::class, array('label' => 'Laden'))
	    		->getForm();
	    	
	    	$frmSelectTournament->handleRequest($request);
	    	if($frmSelectTournament->isValid())
	    	{
	    		$tournament = $frmSelectTournament->get('tournament')->getData();
	    		return $this->redirect($this->generateUrl('live_index', array('tournamentId' => $tournament->getId())));
	    	}
	    	return array(
	    			'frmSelectTournament' => $frmSelectTournament->createView()
	    	);
    	}
    }
}
