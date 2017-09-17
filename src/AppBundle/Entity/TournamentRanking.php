<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use AppBundle\Model\Tournament\TournamentRankingInterface;

/**
 * TournamentRank
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TournamentRankingRepository")
 */
class TournamentRanking implements TournamentRankingInterface
{
    /** 
     * @ORM\Id()
     * @ORM\ManyToOne(targetEntity="Tournament", inversedBy="ranking") 
     * @ORM\JoinColumn(name="tournament_id", referencedColumnName="id", nullable=false) 
     */
    protected $tournament;

    /**
     * @ORM\Id()
     * @ORM\ManyToOne(targetEntity="Player", inversedBy="tournamentRanking", fetch="EAGER")
     * @ORM\JoinColumn(name="player_id", referencedColumnName="id", nullable=false)
     */
    protected $player;
    
    /**
     * @ORM\ManyToOne(targetEntity="Player")
     * @ORM\JoinColumn(name="kicked_by_player_id", referencedColumnName="id", nullable=true)
     */
    private $kickedByPlayer;
    
    /**
     * @ORM\Column(name="kicked_at", type="datetime", nullable=true)
     * 
     * @var \DateTime
     */
    private $kickedAt;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="rank", type="integer", nullable=true)
     */
    private $rank;

    /**
     * @var string
     *
     * @ORM\Column(name="comment", type="string", length=255, nullable=true)
     */
    private $comment;
 
    public function __construct(Tournament $tournament, Player $player)
    {
    	$this->tournament = $tournament;
    	$this->player = $player;
    	
    	return $this;
    }

    public function getTournament()
    {
    	return $this->tournament;
    }

    public function getPlayer()
    {
    	return $this->player;
    }

    public function getKickedByPlayer()
    {
        return $this->kickedByPlayer;
    }

    public function setKickedByPlayer(Player $player)
    {
        $this->kickedByPlayer = $player;
        return $this;
    }

    public function getKickedAt()
    {
        return $this->kickedAt;
    }

    public function setKickedAt(\DateTime $kickedAt = null)
    {
        $this->kickedAt = $kickedAt;
        return $this;
    }
 
    /**
     * Set rank
     *
     * @param integer $rank
     *
     * @return TournamentRank
     */
    public function setRank($rank)
    {
        $this->rank = $rank;

        return $this;
    }

    /**
     * Get rank
     *
     * @return integer
     */
    public function getRank()
    {
        return $this->rank;
    }
    
    public function setComment($comment)
    {
    	$this->comment = $comment;
    	return $this;
    }
    
    public function getComment()
    {
    	return $this->comment;
    }
}

