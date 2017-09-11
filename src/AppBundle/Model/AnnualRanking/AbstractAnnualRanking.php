<?php
namespace AppBundle\Model\AnnualRanking;

use AppBundle\Model\Player\PlayerInterface;
use AppBundle\Model\Tournament\TournamentInterface;
use AppBundle\Entity\Player;
use AppBundle\Entity\Tournament;

/**
 * AbstractAnnualRanking ## 5-Jahreswertung
 * 
 */
abstract class AbstractAnnualRanking implements AnnualRankingInterface
{

    /**
     *
     * @var integer
     */
    protected $rank = null;

    /**
     *
     * @var integer
     */
    protected $sumPoints = 0;

    /**
     *
     * @var integer
     */
    protected $points = 0;
    
    /**
     *
     * @var int
     */
    protected $year;

    /**
     *
     * @var Player
     *
     */
    protected $player;

    /**
     *
     * @var Tournament
     */
    protected $tournament;

    /**
     * Erstellt neue Instanz der Klasse
     *
     * Wenn Turnier übergeben wurde, wird das Jahr aus dem Turnierdatum
     * extrahiert
     *
     * @param PlayerInterface $player            
     * @param TournamentInterface $tournament            
     * @return AnnualRanking
     */
    public function __construct (PlayerInterface $player = null, TournamentInterface $tournament = null)
    {
        if (! is_null($tournament)) 
        {
            $this->tournament = $tournament;
            $this->year = (int) $tournament->getDate()->format('Y');
        }
        if (! is_null($player))
        {
            $this->setPlayer($player);
        }
                
        return $this;
    }

    public function setRank ($rank = null)
    {
        $this->rank = $rank;
        return $this;
    }

    public function getRank ()
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
    public function setPoints ($points)
    {
        $this->points = $points;
        
        return $this;
    }

    /**
     * Get points
     *
     * @return integer
     */
    public function getPoints ()
    {
        return $this->points;
    }

    /**
     * Setzt Jahr
     *
     * @param int $year            
     * @return AnnualRankingInterface
     */
    public function setYear ($year)
    {
        $this->year = $year;
        return $this;
    }

    /**
     * Gibt Jahr zurück
     *
     * @return int
     */
    public function getYear ()
    {
        return $this->year;
    }

    /**
     * Set sum points
     *
     * @param integer $sum            
     *
     * @return PlayerRanking
     */
    public function setSumPoints ($sum)
    {
        $this->sumPoints = $sum;
        
        return $this;
    }

    /**
     * Get sum points
     *
     * @return integer
     */
    public function getSumPoints ()
    {
        return $this->sumPoints;
    }

    /**
     *
     * @param Player $player            
     * @return AnnualRankingInterface
     */
    public function setPlayer (PlayerInterface $player)
    {
        $this->player = $player;
        if ($player !== null)
            $this->player->addAnnualRanking($this);
        elseif ($this->player !== null)
            $this->player->removeAnnualRanking($this);
        return $this;
    }

    /**
     *
     * @return \AppBundle\Entity\Player
     */
    public function getPlayer ()
    {
        return $this->player;
    }

    /**
     *
     * @param Tournament $tournament            
     * @return AnnualRankingInterface
     */
    public function setTournament (Tournament $tournament)
    {
        $this->tournament = $tournament;
        return $this;
    }

    /**
     *
     * @return \AppBundle\Entity\Tournament
     */
    public function getTournament ()
    {
        return $this->tournament;
    }
}
