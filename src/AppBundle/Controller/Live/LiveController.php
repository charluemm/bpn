<?php

namespace AppBundle\Controller\Live;

use AppBundle\Entity\Tournament;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\Common\Util\Debug;
use AppBundle\Entity\TournamentManager;
use AppBundle\Entity\BlindLevel;
use AppBundle\Entity\TournamentStatus;

/**
 * @author Michael Müller <development@reu-network.de>
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
        else
        {
            $frmSelectTournament = $this->createFormBuilder()
            ->add('tournament', EntityType::class, array(
                    'label' => 'Turnier wählen',
                    'placeholder' => 'Turnier für Live-Ansicht wählen',
                    'class' => 'AppBundle:Tournament',
                    'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('t')
                    ->orderBy('t.date', 'DESC');
                    },
                    'group_by' => 'event'
                            ))
                            ->add('submit', SubmitType::class, array('label' => 'Laden'))
                            ->getForm();
                            
                            $frmSelectTournament->handleRequest($request);
                            if($frmSelectTournament->isValid())
                            {
                                $tournament = $frmSelectTournament->get('tournament')->getData();
                                return $this->redirect($this->generateUrl('live_index', array('tournamentId' => $tournament->getId())));
                            }
                            return array(
                                    'frmSelectTournament' => $frmSelectTournament->createView()
                            );
        }
    }
    
    /**
     * @Route("/ajax/button-status", name="button_status")
     */
    public function buttonStatusAction()
    {
        $output = array();
        $host = "192.168.1.113";
        exec("ping -n 1 $host -w 500", $output);
        
        $status = (int)(preg_grep("/Antwort von $host: Bytes=.*/", $output) !== array() );
        
        return new JsonResponse($status);
    }
    
    /**
     * @Route("/{tournamentId}/raise-blind", name="live_raise_blind")
     */
    public function raiseBlind($tournamentId)
    {
        /** @var TournamentManager $tournamentManager **/
        $tournamentManager = $this->get('bpn.tournament.manager');
        $blindRepo = $this->getDoctrine()->getRepository(BlindLevel::class);
        
        $tournament = $tournamentManager->find($tournamentId);
        $currBlind = $tournament->getBlindLevel();
        $nextBlind = $blindRepo->findOneBy([ 'level' => ($currBlind ? $currBlind->getLevel() : 0) + 1]);
        
        $tournament->setBlindLevel($nextBlind);
        $tournamentManager->update($tournament);
        
        return new JsonResponse(1);
        
    }
    
    /**
     * start/resume tournament
     *
     * return Status 200 if success and not changed
     * return Status 201 if successfull changed
     * 
     * @Route("/{tournamentId}/start", name="live_start_tournament")
     */
    public function start($tournamentId)
    {
        /** @var TournamentManager $tournamentManager **/
        $tournamentManager = $this->get('bpn.tournament.manager');
        
        $tournament = $tournamentManager->find($tournamentId);
        
        // set running if pause or created
        if(in_array($tournament->getCurrentStatus(), [ TournamentStatus::$DESCRIPTION_CREATED, TournamentStatus::$DESCRIPTION_PAUSE]))
        {
            $tournament->setTournamentStatus(new TournamentStatus($tournament, TournamentStatus::$DESCRIPTION_RUNNING));
        }
        // do nothing return false
        else
        {
            return new JsonResponse($tournament->getCurrentStatus(), 200);
        }
        
        $tournamentManager->update($tournament);
        return new JsonResponse($tournament->getCurrentStatus(), 201);
    }
    
    /**
     * pause tournament
     *
     *  @Route("/{tournamentId}/pause", name="live_pause_tournament")
     */
    public function pause($tournamentId)
    {
        /** @var TournamentManager $tournamentManager **/
        $tournamentManager = $this->get('bpn.tournament.manager');
        
        $tournament = $tournamentManager->find($tournamentId);
        
        // set pause if running
        if($tournament->getCurrentStatus() == TournamentStatus::$DESCRIPTION_RUNNING)
        {
            $tournament->setTournamentStatus(new TournamentStatus($tournament, TournamentStatus::$DESCRIPTION_PAUSE));
        }
        // do nothing return false
        else
        {
            return new JsonResponse($tournament->getCurrentStatus(), 200);
        }
        
        $tournamentManager->update($tournament);
        return new JsonResponse($tournament->getCurrentStatus(), 201);
    }
}
