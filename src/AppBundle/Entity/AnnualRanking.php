<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AnnualRanking
 * 5-Jahreswertung
 *
 * @ORM\Table(name="app_annual_ranking",
 * 				uniqueConstraints={@ORM\UniqueConstraint(name="unique_tournament_player", columns={"player_id", "tournament_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Entity\AnnualRankingRepository")
 */
class AnnualRanking
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="rank", type="integer", nullable=true)
     */
    private $rank = null;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="sum_points", type="integer")
     */
    private $sumPoints = 0;

    /**
     * @var Player
     * 
     * @ORM\ManyToOne(targetEntity="Player", inversedBy="annualRanking", fetch="EAGER")
     * @ORM\JoinColumn(name="player_id", referencedColumnName="id", nullable=false)
     */
    protected $player;
    
    /**
     * zugeordnetes Turnier
     * 
     * @ORM\ManyToOne(targetEntity="Tournament", fetch="EAGER")
     * @ORM\JoinColumn(name="tournament_id", referencedColumnName="id", nullable=false)
     * 
     * @var Tournament
     */
    protected $tournament;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    public function __construct(Player $player = null)
    {
    	$this->setPlayer($player);
    	$this->createdAt = new \DateTime();
    }
    
    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    public function setRank($rank = null)
    {
        $this->rank = $rank;
        return $this;
    }
    
    public function getRank()
    {
        return $this->rank;
    }
    
    /**
     * Set points
     *
     * @param integer $points
     *
     * @return PlayerRanking
     */
    public function setPoints($points)
    {
        $this->points = $points;

        return $this;
    }

    /**
     * Get points
     *
     * @return integer
     */
    public function getPoints()
    {
        return $this->points;
    }

    /**
     * Set sum points
     *
     * @param integer $sum
     *
     * @return PlayerRanking
     */
    public function setSumPoints($sum)
    {
        $this->sumPoints = $sum;

        return $this;
    }

    /**
     * Get sum points
     *
     * @return integer
     */
    public function getSumPoints()
    {
        return $this->sumPoints;
    }
    
    /**
     * @param Player $player
     * @return \AppBundle\Entity\AnnualRanking
     */
    public function setPlayer(Player $player)
    {
    	$this->player = $player;
    	if($player !== null)
	    	$this->player->addAnnualRanking($this);
    	elseif($this->player !== null)
    		$this->player->removeAnnualRanking($this);
    	return $this;
    }

    /**
     * @return \AppBundle\Entity\Player
     */
    public function getPlayer()
    {
    	return $this->player;
    }
    
    /**
     * 
     * @param Tournament $tournament
     * @return \AppBundle\Entity\AnnualRanking
     */
    public function setTournament(Tournament $tournament)
    {
        $this->tournament = $tournament;
        return $this;
    }
    
    /**
     * 
     * @return \AppBundle\Entity\Tournament
     */
    public function getTournament()
    {
        return $this->tournament;
    }
    
    /**
     * @return DateTime
     */
    public function getCreatedAt()
    {
    	return $this->createdAt;
    }
}


