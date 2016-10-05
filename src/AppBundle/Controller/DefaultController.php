<?php

namespace Reu\Pokernight\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DefaultController extends Controller
{
	/**
	 * @Route("/", name="index")
	 */
    public function indexAction($name = "Test")
    {
    	$em = $this->getDoctrine()->getManager();
    	
        return $this->render('PokernightAppBundle:Default:index.html.twig', array(
        		'name' => "Test",
        ));
    }
	/**
	 * @Route("/annual-ranking", name="annualranking")
	 */
    public function annualRankingAction()
    {
    	$em = $this->getDoctrine()->getManager();
    	$listAnnualRanking = $em->getRepository('PokernightAppBundle:AnnualRanking')->findLastGroupByPlayer();
    	
        return $this->render('PokernightAppBundle:Default:annualRanking.html.twig', array(
        		'annualRanking' => $listAnnualRanking
        ));
    }
}

