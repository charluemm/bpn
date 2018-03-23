<?php

namespace AppBundle\Controller;

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

/**
 * @author Michael Müller <development@reu-network.de>
 *
 * @Route("/dmx")
 */
class DmxController extends Controller
{   
    /**
     * @Route("/draw/light/enable", name="dmx_draw_start")
     */
    public function startDrawLigth()
    {
        $output = array();
        $return = "";
        
        $sock = fsockopen("127.0.0.1", 10160);
        fputs($sock, "start_scene {93B4A812-4039-4794-933B-DA7B6D5C6EFA}".PHP_EOL);
        fclose($sock);
        return new JsonResponse($return);
    }
    
    /**
     * @Route("/draw/light/disable", name="dmx_draw_stop")
     */
    public function stopDrawLigth()
    {
        $output = array();
        $return = "";
        
        $sock = fsockopen("127.0.0.1", 10160);
        fputs($sock, "stop_scene {93B4A812-4039-4794-933B-DA7B6D5C6EFA}".PHP_EOL);
        fclose($sock);
        return new JsonResponse($return);
    }
    
    /**
     * @Route("/nextblind", name="dmx_next_blind")
     */
    public function startNextBlind()
    {
        $output = array();
        $return = "";
        
        $sock = fsockopen("127.0.0.1", 10160);
        fputs($sock, "start_scene {C246C458-4171-4F2F-BB94-994DB2828232}".PHP_EOL);
        fclose($sock);
        return new JsonResponse($return);
    }
}
