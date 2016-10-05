<?php

namespace Reu\Pokernight\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AnnualRanking
 * 5-Jahreswertung
 *
 * @ORM\Table(name="app_annual_ranking",
 * 				uniqueConstraints={@ORM\UniqueConstraint(name="unique_date_player", columns={"player_id", "date"})})
 * @ORM\Entity(repositoryClass="Reu\Pokernight\AppBundle\Entity\AnnualRankingRepository")
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
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     * @var integer
     *
     * @ORM\Column(name="points", type="integer")
     */
    private $points = 0;

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

    /**
     * Set date
     *
     * @param \DateTime $date
     *
     * @return PlayerRanking
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
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
     * @return \Reu\Pokernight\AppBundle\Entity\AnnualRanking
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
     * @return \Reu\Pokernight\AppBundle\Entity\Player
     */
    public function getPlayer()
    {
    	return $this->player;
    }
    
    /**
     * @return DateTime
     */
    public function getCreatedAt()
    {
    	return $this->createdAt;
    }
}


