<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity\AnnualRanking\AnnualRanking;
use AppBundle\Entity\AnnualRanking\AnnualRankingManager;

class DefaultController extends Controller
{
	/**
	 * @Route("/", name="index")
	 */
    public function indexAction($name = "Test")
    {
    	//$em = $this->getDoctrine()->getManager();
    	
        return $this->render('AppBundle:Default:index.html.twig', array(
        		'name' => "Test",
        ));
    }
	/**
	 * @Route("/annual-ranking", name="annualranking")
	 */
    public function annualRankingAction()
    {
        /** @var AnnualRankingManager $annualRankingManager **/
        $annualRankingManager = $this->get('bpn.annual_ranking.manager');
        
        return $this->render('AppBundle:Default:annualRanking.html.twig', array(
        		'annualRanking' => $annualRankingManager->findCurrentRanking()
        ));
    }
}

