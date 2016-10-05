<?php

namespace Reu\Pokernight\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Util\Debug;
use Reu\Pokernight\AppBundle\Entity\Player;
use Reu\Pokernight\AppBundle\Entity\Tournament;
use Reu\Pokernight\AppBundle\Entity\Seat;

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
     * @Template("PokernightAppBundle:Live:live.html.twig")
     */
    public function showAction(Request $request, $tournamentId = null)
    {
    	$em = $this->getDoctrine()->getManager();
    	
    	if(!empty($tournamentId))
    	{
    		/* @var $tournament Tournament */
    		$tournament = $em->getRepository('PokernightAppBundle:Tournament')->find($tournamentId);
    		
    		return array(
    				'tournament' => $tournament
    		);
    	}
        return array(
        );    
    }
    
    /**
     * @Route("/{tournamentId}/draw", name="live_draw")
     * @Template("PokernightAppBundle:Live:draw.html.twig")
     */
    public function liveDrawAction($tournamentId)
    {
    	$em = $this->getDoctrine()->getManager();
    	/* @var $tournament Tournament */
   		$tournament = $em->getRepository('PokernightAppBundle:Tournament')->find($tournamentId);

   		// alle Turnierteilnehmer
   		$playerList = array();
    	foreach($tournament->getRanking() as $ranking)
    		$playerList[] = $ranking->getPlayer();
    		
    	// Spieler 5-Jahreswertung
    	$listAnnualRanking = $em->getRepository('PokernightAppBundle:AnnualRanking')->findLastGroupByPlayer();
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
     * @Template("PokernightAppBundle:Live:single_table.html.twig")
     */
    public function singleTableAction()
    {
    	$em = $this->getDoctrine()->getManager();
    	$allPlayer = $em->getRepository('PokernightAppBundle:Player')->findAll();
    	 
        return array(
        		'all_player' => $allPlayer
        );    
    }

    public function addSeat($playerId, $tableId)
    {
    	
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
