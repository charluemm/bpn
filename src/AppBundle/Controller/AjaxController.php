<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\JsonResponse;
use AppBundle\Entity\Seat;

/**
 *
 * @author Michael Müller <development@reu-network.de>
 *        
 * @Route("/ajax")
 */
class AjaxController extends Controller {
	
	/**
	 * Gibt Livedaten zu einem Turnier als JSON zurück
	 * 
	 * @Route("/tournament/{id}/live/info", name="ajax_tournament_live_data")
	 * @Method("POST")        
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
		
		if (empty ( $tournament ))
			die ( "Kein Turnier mit dieser ID vorhanden" );
		
		$activePlayer = $tournamentRepo->countActivePlayer ( $id );
		$countPlayer = count ( $tournament->getRanking () );
		
		$return = array (
				'player' => array (
						'current' => $activePlayer,
						'count' => $countPlayer 
				),
				'blind' => array (
						'current' => '10/20',
						'next' => "20/40",
						'next_time' => time () 
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
