<?php

namespace AppBundle\Model\Tournament;

use Doctrine\Common\Collections\ArrayCollection;
use AppBundle\Model\Tournament\TournamentInterface;
use AppBundle\Entity\Location;
use AppBundle\Entity\TournamentRanking;
use AppBundle\Entity\TournamentTable;
use AppBundle\Entity\Event;

/**
 * AbstractTournament
 */
abstract class AbstractTournament implements TournamentInterface
{
   /**
     * @var string
     *
     */
    protected $name;
    
    /**
     * @var \DateTime
     *
     */
    protected $date;

    /**
     * @var string
     */
    protected $description;
    
    /**
     * @var boolean
     * 
     */
    protected $mainTournament;
    
    /**
     * @var Location
     *
     **/
    protected $location;
    
    /**
     * @var Event
     * 
     **/
    protected $event;
    
    /**
     * @var ArrayCollection
     *  
     * 
     */
    protected $ranking;
    
    /**
     * @var ArrayCollection
     *  
     */
    protected $tables;
    
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
    /**
     * 
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
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
