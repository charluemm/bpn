<?php

namespace AppBundle\Controller\Live;

use AppBundle\Entity\Player;
use AppBundle\Entity\Tournament;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\Common\Util\Debug;
use AppBundle\Entity\TournamentManager;
use AppBundle\Entity\AnnualRanking\AnnualRankingManager;

/**
 * @author Michael Müller <development@reu-network.de>
 *
 */
class DrawController extends Controller
{
    /**
     * @Route("/{tournamentId}/draw", name="live_draw")
     * @Template("AppBundle:Live:draw.html.twig")
     */
    public function liveDrawAction($tournamentId)
    {
    	/** @var TournamentManager $tournamentManager **/
    	$tournamentManager = $this->get('bpn.tournament.manager');
    	/** @var AnnualRankingManager $annualRankingManager **/
    	$annualRankingManager = $this->get('bpn.annual_ranking.manager');
    	
    	/* @var $tournament Tournament */
   		$tournament = $tournamentManager->find($tournamentId);

   		// alle Turnierteilnehmer
   		$playerList = array();
    	foreach($tournament->getRanking() as $ranking)
    		$playerList[] = $ranking->getPlayer();
    		
    	// Spieler 5-Jahreswertung
    	$listAnnualRanking = $annualRankingManager->findCurrentRanking();
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
}
