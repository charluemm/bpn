<?php

namespace AppBundle\Controller\Live;

use AppBundle\Entity\AnnualRanking\AnnualRankingManager;
use AppBundle\Entity\Player;
use AppBundle\Entity\Tournament;
use AppBundle\Entity\TournamentManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * @author Michael Müller <development@reu-network.de>
 * 
 *
 */
class OverviewController extends Controller
{
    /**
     * TODO: muss noch angepasst werden
     *      * nur zum Test
     *      
     * @Route("/single-table", name="live_single_table")
     * @Template("AppBundle:Live:single_table.html.twig")
     */
    public function singleTableAction()
    {
    	$em = $this->getDoctrine()->getManager();
    	$allPlayer = $em->getRepository('AppBundle:Player')->findAll();
    	 
        return array(
        		'all_player' => $allPlayer
        );    
    }
    
    /**
     * @Route("/{tournamentId}/overview", name="live_tournament_overview")
     * @Template("AppBundle:Live:tournament_overview.html.twig")
     */
    public function tournamentOverviewAction($tournamentId)
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
        return array(
        );
    }
    
    /**
     * @Route("/{tournamentId}/annual-ranking", name="live_tournament_annual_ranking")
     * @Template("AppBundle:Live:annual_ranking.html.twig")
     */
    public function annualRankingAction($tournamentId)
    {
        if(!empty($tournamentId))
        {
            /** @var TournamentManager $tournamentManager **/
            $tournamentManager = $this->get('bpn.tournament.manager');
            /** @var AnnualRankingManager $annualRankingManager **/
            $annualRankingManager = $this->get('bpn.annual_ranking.manager');
            
            $tournament = $tournamentManager->find($tournamentId); 
            
            return array(
                'tournament' => $tournament,
                'ranking_data' => $annualRankingManager->findAdvancedForTournament($tournament)
            );
        }
        
        return array(
        );
    }
}
