<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use AppBundle\Entity\Tournament;
use Symfony\Component\Config\Definition\Exception\InvalidTypeException;

/**
 * TournamentStatus
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TournamentStatusRepository")
 */
class TournamentStatus
{
    static $DESCRIPTION_CREATED = "created";
    static $DESCRIPTION_RUNNING = "running";
    static $DESCRIPTION_PAUSE = "paused";
    static $DESCRIPRION_FINISH = "finished";
    
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /** 
     * @ORM\ManyToOne(targetEntity="Tournament", inversedBy="ranking") 
     * @ORM\JoinColumn(name="tournament_id", referencedColumnName="id", nullable=false) 
     */
    protected $tournament;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255, nullable=true)
     */
    private $description;
 
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;
    
    /**
     * 
     * @param Tournament $tournament
     * @param unknown $description
     * @throws InvalidTypeException
     * @return \AppBundle\Entity\TournamentStatus
     */
    public function __construct(Tournament $tournament, $description = null)
    {
    	if(($description !== null) && ($this->isValidDescription($description) == false))
    	{
    	    throw new InvalidTypeException("\"$description\" not a valid description. Choose one of \"".implode("\",", self::getValidDescriptions())."\"");
    	}
    	
    	$this->tournament = $tournament;
    	$this->description = $description;
    	$this->createdAt = new \DateTime();
    	
    	return $this;
    }

    /**
     * 
     * @return number
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * 
     * {@inheritDoc}
     * @see \AppBundle\Model\Tournament\TournamentRankingInterface::getTournament()
     */
    public function getTournament()
    {
    	return $this->tournament;
    }
    
    /**
     * 
     * @param unknown $description
     * @return \AppBundle\Entity\TournamentStatus
     */
    public function setDescription($description)
    {
        if(! $this->isValidDescription($description))
        {
            throw new InvalidTypeException("\"$description\" not a valid description. Choose one of \"".implode("\",", self::getValidDescriptions())."\"");
        }
        
        $this->description = $description;
        return $this;
    }
    
    /**
     * 
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }
    
    /**
     * 
     * @return DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }
    
    /**
     * 
     * @return string[]
     */
    private function getValidDescriptions()
    {
        return [
                self::$DESCRIPRION_FINISH,
                self::$DESCRIPTION_CREATED,
                self::$DESCRIPTION_PAUSE,
                self::$DESCRIPTION_RUNNING
        ];
    }
    
    /**
     * 
     * @param string $description
     * @return boolean
     */
    static function isValidDescription($description)
    {
        if(!in_array($description, self::getValidDescriptions()))
        {
            return false; 
        }
        
        return true;
    }
}
