<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 *
 * @author Michael Müller <development@reu-network.de>
 *        
 * @Route("/ajax")
 * @Method("POST")        
 */
class AjaxController extends Controller {
	
	/**
	 * Gibt Livedaten zu einem Turnier als JSON zurück
	 * 
	 * @Route("/tournament/{id}/live", name="ajax_tournament_live_data")
	 *
	 * @param Request $request        	
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function getTournamentInfo(Request $request) {
		$id = $request->get ( 'id' );
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
}
