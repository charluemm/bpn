<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\JsonResponse;
use AppBundle\Entity\Seat;
use AppBundle\Entity\Tournament;
use AppBundle\Entity\TournamentRanking;
use AppBundle\Entity\BlindLevel;
use Doctrine\Common\Util\Debug;

/**
 *
 * @author Michael Müller <development@reu-network.de>
 *        
 * @Route("/ajax")
 */
class AjaxController extends Controller 
{
    const DEFAULT_BLIND_TIMER = 900; // 15min
    
	/**
	 * Gibt für ein bestimmtes Turnier das aktuelle Ranking zurück
	 *
	 * @Route("/tournament/{id}/ranking", name="ajax_tournament_ranking")
	 *
	 * @param Request $request
	 * @return \Symfony\Component\HttpFoundation\JsonResponse
	 */
	public function getTournamentRankingAction($id)
	{
		$em = $this->getDoctrine()->getManager();
		if (empty ( $id ))
			die ( "Skriptaufruf fehlgeschlagen." );
		
		$em = $this->getDoctrine ()->getManager ();
		$tournamentRepo = $em->getRepository ( 'AppBundle:Tournament' );
		/** @var $tournament Tournament **/
		$tournament = $tournamentRepo->find ( $id );
	
		if(empty($tournament))
			return new JsonResponse(array('message' => "Es wurde kein Turnier mit der ID $id gefunden."), 500);
		
		$content = array();
		
		/** @var $rank TournamentRanking **/
		foreach($tournament->getRanking() as $rank)
		{
			$content[] = array('player' => $rank->getPlayer()->getNickname(), 'rank' => $rank->getRank() ? : "");
		}
		
		return new JsonResponse(array_reverse($content), 200);
	}
	
	/**
	 * Gibt Livedaten zu einem Turnier als JSON zurück
	 * 
	 * @Route("/tournament/{id}/live/info", name="ajax_tournament_live_data")
	 *
	 * @param Request $request        	
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function getTournamentInfoAction($id) 
	{	
		if (empty ( $id ))
			die ( "Skriptaufruf fehlgeschlagen." );
		
		$em = $this->getDoctrine ()->getManager ();
		$tournamentRepo = $em->getRepository ( 'AppBundle:Tournament' );
		$tournament = $tournamentRepo->find ( $id );
	
		$blindRepo = $em->getRepository(BlindLevel::class);
		
		if (empty ( $tournament ))
			die ( "Kein Turnier mit dieser ID vorhanden" );
	
		// generate data
		$activePlayer = $tournamentRepo->countActivePlayer ( $id );
		$countPlayer = count ( $tournament->getRanking () );
	
		$currBlind = $tournament->getBlindLevel();
		$nextBlind = $blindRepo->findOneBy([ 'level' => ($currBlind ? $currBlind->getLevel() : 0) + 1]);
		
		$raiseAt = $tournament->getLastBlindRaiseAt();
		// setze ablauftimer nur, wenn bereits eine Blinderhöhung anstand
		if(!empty($raiseAt))
		{
		    $raiseAt->add(date_interval_create_from_date_string(self::DEFAULT_BLIND_TIMER." seconds"));
		    $raiseAt = $raiseAt->format('r');
		}
		else
		{
		    $raiseAt = "";
		}

		// create output
		$currBlind = $currBlind == null ? "" : $currBlind->getName();		
		$nextBlind = $nextBlind == null ? "" : $nextBlind->getName();
		
		$return = array (
				'player' => array (
						'current' => $activePlayer,
						'count' => $countPlayer 
				),
				'blind' => array (
						'current' => $currBlind,
						'next' => $nextBlind,
						'raiseAt' => $raiseAt
				        
				) 
		);
		
		return new Response ( json_encode ( $return ) );
	}
	
	/**
	 * Legt einen neuen Sitz für einen übergebenen Tisch und Spieler an
	 *
	 * @Route("/table/add/seat-list", name="ajax_table_add_seats")
	 * @Method("POST")
	 *
	 * @param Request $request
	 * @return \Symfony\Component\HttpFoundation\JsonResponse
	 */
	public function setTableSeatListAction(Request $request)
	{
		$tableId = $request->get('table');
		$seatList = $request->get('list');
		
		if(empty($seatList) || empty($tableId))
			return new JsonResponse(array('message' => 'Aufruf fehlgeschlagen'), 500);
		
		$em = $this->getDoctrine()->getManager();
		$playerRepo = $em->getRepository("AppBundle:Player");
		
		/* @var $table TournamentTable */
		$table = $em->getRepository("AppBundle:TournamentTable")->find($tableId);

		if(empty($table))
			return new JsonResponse(array('message' => "Es wurde kein Tisch mit der ID $tableId gefunden."), 500);
		
		// TODO: ALte Einträge löschen
		foreach($table->getSeats() as $seat)
			$em->remove($seat);
		$em->flush();
		
		foreach ($seatList as $number => $playerId)
		{
			$player = $playerRepo->find($playerId);
			if(empty($player))
			{
				return new JsonResponse(array('message' => "Es wurde kein Spieler mit der ID $playerId gefunden."), 500);
			}
			else
			{
				$newSeat = new Seat($table, $player);
				$newSeat
					->setNumber($number + 1)
					->setName("Platz ".trim($number + 1));
				$em->persist($newSeat);	
			}
		}
		
		$em->flush();
		
		return new JsonResponse(array('message' => "OK"), 200);
	}
}
