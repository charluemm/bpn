<?php

namespace Reu\Pokernight\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * TournamentTable
 *
 * @ORM\Table(name="tournament_table")
 * @ORM\Entity(repositoryClass="Reu\Pokernight\AppBundle\Entity\TableRepository")
 */
class TournamentTable
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
     * @ORM\ManyToOne(targetEntity="Tournament", inversedBy="tables") 
     * @ORM\JoinColumn(name="tournament_id", referencedColumnName="id", nullable=false) 
     */
    protected $tournament;
    
    /**
     * @var Collection
     * @ORM\OneToMany(targetEntity="Seat", mappedBy="table")
     */
    protected $seats;

    /**
     * @var integer
     * 
     * @ORM\Column(name="number", type="integer", nullable=false)
     */
    private $number;
    
    /**
     * @var boolean
     * 
     * @ORM\Column(name="final", type="boolean")
     */
    private $finalTable;
    
    /**
     * @var integer
     * 
     * @ORM\Column(name="max_seats", type="integer", nullable=false)
     */
    private $maxSeats;
    
    /**
     * @var string
     *
     * @ORM\Column(name="comment", type="string", length=255, nullable=true)
     */
    private $comment;
 
    public function __construct(Tournament $tournament)
    {
    	$this->tournament = $tournament;
    	$this->seats = new ArrayCollection(); 
    	$this->finalTable = false;
    	return $this;
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
     * Set number
     *
     * @param integer $number
     *
     * @return Table
     */
    public function setNumber($number)
    {
        $this->number = $number;

        return $this;
    }

    /**
     * Get number
     *
     * @return integer
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * Set finalTable
     *
     * @param boolean $finalTable
     *
     * @return Table
     */
    public function setFinalTable($finalTable)
    {
        $this->finalTable = $finalTable;

        return $this;
    }

    /**
     * is finalTable
     *
     * @return boolean
     */
    public function isFinalTable()
    {
        return $this->finalTable;
    }

    /**
     * Set maxSeats
     *
     * @param integer $maxSeats
     *
     * @return Table
     */
    public function setMaxSeats($maxSeats)
    {
        $this->maxSeats = $maxSeats;

        return $this;
    }

    /**
     * Get maxSeats
     *
     * @return integer
     */
    public function getMaxSeats()
    {
        return $this->maxSeats;
    }

    /**
     * Set comment
     *
     * @param string $comment
     *
     * @return Table
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get comment
     *
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Set tournament
     *
     * @param \Reu\Pokernight\AppBundle\Entity\Tournament $tournament
     *
     * @return Table
     */
    public function setTournament(\Reu\Pokernight\AppBundle\Entity\Tournament $tournament)
    {
        $this->tournament = $tournament;

        return $this;
    }

    /**
     * Get tournament
     *
     * @return \Reu\Pokernight\AppBundle\Entity\Tournament
     */
    public function getTournament()
    {
        return $this->tournament;
    }

    /**
     * Add seat
     *
     * @param \Reu\Pokernight\AppBundle\Entity\Seat $seat
     *
     * @return Table
     */
    public function addSeat(\Reu\Pokernight\AppBundle\Entity\Seat $seat)
    {
    	if(!$this->seats->contains($seat))
        	$this->seats->add($seat);

        return $this;
    }

    /**
     * Remove seat
     *
     * @param \Reu\Pokernight\AppBundle\Entity\Seat $seat
     */
    public function removeSeat(\Reu\Pokernight\AppBundle\Entity\Seat $seat)
    {
    	if($this->seats->contains($seat))
        	return $this->seats->removeElement($seat);
    	else
    		return null;
    }

    /**
     * Get seats
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSeats()
    {
        return $this->seats;
    }
}
