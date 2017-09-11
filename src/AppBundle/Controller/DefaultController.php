<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity\AnnualRanking;

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
    	$em = $this->getDoctrine()->getManager();
    	
    	$playerRepo = $em->getRepository('AppBundle:AnnualRanking');
    		
    	$listAnnualRanking = $em->getRepository('AppBundle:AnnualRanking')->findLastGroupByPlayer();
    	
    	$playerList = array();
    	foreach ($playerRepo->findAll() as $player)
    	{
    	    foreach ($player->getAnnualHistory() as $annualHistory)
    	    {
    	        array_push($playerList[$player->getId()]['history'], $value1);
    	        
    	    }
    	}
    	
    	foreach($listAnnualRanking as $rank)
    	{
    	    $playerList[] = array(
    	            'points' => $rank->getSumPoints(),
    	            'player' => $rank->getPlayer()
    	            
    	    );
    	}
    	
        return $this->render('AppBundle:Default:annualRanking.html.twig', array(
        		'annualRanking' => $listAnnualRanking,
                'playerList' => $playerList
        ));
    }
}

