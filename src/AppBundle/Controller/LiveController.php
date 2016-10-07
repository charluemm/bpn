<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Player;
use AppBundle\Entity\Tournament;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Michael Müller <development@reu-network.de>
 * 
 * @Route("/live")
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
    	return array(
    	);
    }
    
    /**
     * @Route("/{tournamentId}/draw", name="live_draw")
     * @Template("AppBundle:Live:draw.html.twig")
     */
    public function liveDrawAction($tournamentId)
    {
    	$em = $this->getDoctrine()->getManager();
    	/* @var $tournament Tournament */
   		$tournament = $em->getRepository('AppBundle:Tournament')->find($tournamentId);

   		// alle Turnierteilnehmer
   		$playerList = array();
    	foreach($tournament->getRanking() as $ranking)
    		$playerList[] = $ranking->getPlayer();
    		
    	// Spieler 5-Jahreswertung
    	$listAnnualRanking = $em->getRepository('AppBundle:AnnualRanking')->findLastGroupByPlayer();
    	$annualPlayerList = array();
    	foreach($listAnnualRanking as $annualRanking)
    		$annualPlayerList[] = $annualRanking->getPlayer();
    	
    	// Spieler nach Ranking sortieren
    	$groupedPlayer = \array_intersect($annualPlayerList, $playerList);
    	usort($groupedPlayer, function(Player $a,Player $b)
	    {
    		if($b->getAnnualRankingPoints() < $a->getAnnualRankingPoints())
    			return -1;
    		if($a->getAnnualRankingPoints() == $b->getAnnualRankingPoints())
    			return null;
    		return 1;
	    });

    	// Spieler gruppieren
    	$groupedPlayer = $this->partition($groupedPlayer,3);
    	$groupedPlayer = \array_reverse($groupedPlayer);
    	// Turniertische laden
    	$tables = $tournament->getTables();
    	
        return array(
        		'tournament' => $tournament,
        		'table_list' => $tables,
        		'player_list' => $playerList,
        		'grouped_list' => $groupedPlayer
        );    
    }
    
    /**
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
     *
     * @param Array $list
     * @param int $p
     * @return multitype:multitype:
     * @link http://www.php.net/manual/en/function.array-chunk.php#75022
     */
    private function partition(Array $list, $p) 
    {
    	$list = \array_reverse($list);
    	$listlen = count($list);
    	$partlen = floor($listlen / $p);
    	$partrem = $listlen % $p;
    	$partition = array();
    	$mark = 0;
    	for($px = 0; $px < $p; $px ++) 
    	{
    		$incr = ($px < $partrem) ? $partlen + 1 : $partlen;
    		$partition[$px] = array_slice($list, $mark, $incr);
    		$mark += $incr;
    	}
    	
    	for($i = 0; $i < $p; $i++)
    	{
    		$partition[$i] = \array_reverse($partition[$i]);
    	}
    	
    	return $partition;
    }
    
    private function cmp(Player $a,Player $b)
    {
    	return strcmp($a->getAnnualRankingPoints(), $b->getAnnualRankingPoints());
    }
}
