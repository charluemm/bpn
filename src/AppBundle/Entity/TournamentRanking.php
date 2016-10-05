<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TournamentRank
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="AppBundle\Entity\TournamentRankingRepository")
 */
class TournamentRanking
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

