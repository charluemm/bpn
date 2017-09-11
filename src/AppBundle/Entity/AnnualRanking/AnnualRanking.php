<?php
namespace AppBundle\Entity\AnnualRanking;

use AppBundle\Entity\Player;
use AppBundle\Entity\Tournament;
use AppBundle\Model\AnnualRanking\AbstractAnnualRanking;
use Doctrine\ORM\Mapping as ORM;

/**
 * AnnualRanking
 * 5-Jahreswertung
 *
 * @ORM\Table(name="app_annual_ranking", uniqueConstraints={
 *   @ORM\UniqueConstraint(name="unique_tournament_player", columns={"player_id", "tournament_id"}),
 *   @ORM\UniqueConstraint(name="unique_tournament_rank", columns={"tournament_id", "rank"})
 * })
 * @ORM\Entity(repositoryClass="AppBundle\Repository\AnnualRankingRepository")
 */
class AnnualRanking extends AbstractAnnualRanking
{

    /**
     *  @ORM\Column(name="id", type="integer")
     *  @ORM\Id
     *  @ORM\GeneratedValue(strategy="AUTO")
     *  
     * @var integer
     */
    protected $id;

    /**
     * @ORM\Column(name="rank", type="integer", nullable=true)
     * 
     * @var integer 
     */
    protected $rank = null;

    /**
     * @ORM\Column(name="sum_points", type="integer")
     * 
     * @var integer 
     */
    protected $sumPoints = 0;

    /**
     * @ORM\Column(name="points", type="integer")
     *
     * @var integer
     */
    protected $points = 0;
    
    /**
     * @ORM\Column(name="year", type="integer", length=4, nullable=false)
     * 
     * @var int 
     */
    protected $year;

    /**
     *
     * @var Player 
     * 
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Player", inversedBy="annualRanking", fetch="EAGER")
     * @ORM\JoinColumn(name="player_id", referencedColumnName="id", nullable=false)
     */
    protected $player;

    /**
     * zugeordnetes Turnier
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Tournament", fetch="EAGER")
     * @ORM\JoinColumn(name="tournament_id", referencedColumnName="id", nullable=false)
     *
     * @var Tournament
     */
    protected $tournament;

    /**
     *
     * @var \DateTime @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    public function __construct(Player $player = null, Tournament $tournament = null)
    {
        parent::__construct($player, $tournament);
        
        $this->createdAt = new \DateTime();
        return $this;
    }
    /**
     * Get id
     *
     * @return integer
     */
    public function getId ()
    {
        return $this->id;
    }
    
    /**
     *
     * @return DateTime
     */
    public function getCreatedAt ()
    {
        return $this->createdAt;
    }
}
