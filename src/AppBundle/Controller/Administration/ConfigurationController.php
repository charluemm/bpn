<?php
namespace AppBundle\Controller\Administration;

use AppBundle\Entity\Tournament;
use AppBundle\Form\AddRankingType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;
use AppBundle\Form\TournamentRankType;
use AppBundle\Form\RankingFieldType;
use Doctrine\ORM\Query\Expr;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use AppBundle\Entity\TournamentRanking;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use AppBundle\Entity\TournamentRanking\TournamentRankingManager;

/**
 */
class AdministrationController extends Controller 
{
	
}
