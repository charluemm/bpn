<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Tournament
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="AppBundle\Entity\TournamentRepository")
 */
class Tournament
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
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="date")
     */
    private $date;

    /**
     * @var string
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;
    
    /**
     * @var boolean
     * 
     * @ORM\Column(name="is_main", type="boolean", options={"default"=false})
     */
    private $mainTournament;
    
    /**
     * @var Location
     *
     * @ORM\ManyToOne(targetEntity="Location")
     * @ORM\JoinColumn(name="location_id", referencedColumnName="id")
     **/
    private $location;
    
    /**
     * @var Event
     * 
     * @ORM\ManyToOne(targetEntity="Event", inversedBy="tournaments")
     * @ORM\JoinColumn(name="event_id", referencedColumnName="id")
     **/
    private $event;
    
    /**
     * @var ArrayCollection
     *  
     * @ORM\OneToMany(targetEntity="TournamentRanking", mappedBy="tournament") 
     * @ORM\OrderBy({"rank" = "ASC", "player" = "ASC"})     
     */
    private $ranking;
    
    /**
     * @var ArrayCollection
     *  
     * @ORM\OneToMany(targetEntity="TournamentTable", mappedBy="tournament") 
     * @ORM\OrderBy({"number" = "ASC"})     
     */
    private $tables;
    
    public function __construct(Event $event = null)
    {
    	$this->ranking = new ArrayCollection();
    	$this->tables = new ArrayCollection();
    	$this->setEvent($event);
    	$this->mainTournament = false;
    }
    
    public function __toString()
    {
    	return $this->getEvent().': '.$this->getName();
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
     * Set name
     *
     * @param string $name
     *
     * @return Tournament
     */
    public function setName($name)
    {
    	$this->name = $name;
    
    	return $this;
    }
    
    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
    	return $this->name;
    }
    
    /**
     * Set date
     *
     * @param \DateTime $date
     *
     * @return Tournament
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
    
    public function setDescription($description)
    {
    	$this->description = $description;
    	return $this;
    }
    
    public function getDescription()
    {
    	return $this->description;
    }

	/**
	 * Set main tournament flag
	 * 
	 * @param boolean $value
	 * @return Tournament
	 */
	public function setMainTournament($value = true)
	{
		$this->mainTournament = $value;
		return $this;
	}
	
	/**
	 * Is main tournament
	 * 
	 * @return booelan TRUE if is main tournament
	 */
	public function isMainTournament()
	{
		return $this->mainTournament;
	}
	
    /**
     * Set location
     *
     * @param Location $location
     *
     * @return Event
     */
    public function setLocation(Location $location = null)
    {
    	$this->location = $location;
    
    	return $this;
    }
    
    /**
     * Get location
     *
     * @return Location
     */
    public function getLocation()
    {
    	return $this->location;
    }

    /**
     * Set event
     *
     * @param \AppBundle\Entity\Event $event
     *
     * @return Tournament
     */
    public function setEvent(\AppBundle\Entity\Event $event = null)
    {
    	if(!empty($event))
    		$event->addTournament($this);
    	
        $this->event = $event;

        return $this;
    }

    /**
     * Get event
     *
     * @return \AppBundle\Entity\Event
     */
    public function getEvent()
    {
        return $this->event;
    }
    
    public function getPlayers()
    {
    	$return = array();
    	foreach ($this->ranking as $ranking)
    		$return[] = $ranking->getPlayer();
    	
    		return $return;
    }
    public function setRanking(ArrayCollection $ranking = null)
    {
    	$this->ranking = $ranking;
    	return $this;
    }
    
    public function getRanking()
    {
    	return $this->ranking;
    }
    
    public function addRanking(TournamentRanking $ranking)
    {
    	if($this->ranking->contains($ranking))
    		return ;
    	
    	$this->ranking->add($ranking);
    	return $this;
    }

    public function removeTournamentRank(TournamentRanking $ranking)
    {
    	if(!$this->ranking->contains($ranking))
    		return false;
    	
    	return $this->ranking->removeElement($ranking);
    }
    
    public function getTables()
    {
    	return $this->tables;
    }
    
    public function addTable(TournamentTable $table)
    {
    	if(!$this->tables->contains($table))
    	{
    		$this->tables->add($table);
    	}
    	return $this;
    }
    
    public function removeTable(TournamentTable $table)
    {
    	if($this->tables->contains($table))
    		return $this->tables->removeElement($table);
    	else
    		return null;
    }
}
