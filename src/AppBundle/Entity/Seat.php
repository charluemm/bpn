<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Config\Definition\BooleanNode;

/**
 * Seat
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="AppBundle\Entity\SeatRepository")
 */
class Seat
{
    /** 
     * @ORM\Id()
     * @ORM\ManyToOne(targetEntity="TournamentTable", inversedBy="seats") 
     * @ORM\JoinColumn(name="table_id", referencedColumnName="id", nullable=false) 
     */
    protected $table;

    /** 
     * @ORM\Id()
     * @ORM\ManyToOne(targetEntity="Player") 
     * @ORM\JoinColumn(name="player_id", referencedColumnName="id", nullable=false) 
     */
    protected $player;

    /**
     * @var integer
     *
     * @ORM\Column(name="number", type="integer", nullable=true)
     */
    private $number;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     */
    private $name;
    
    /**
     * @var boolean
     * 
     * @ORM\Column(name="active", type="boolean")
     */
    private $active;
 
    public function __construct(TournamentTable $table, Player $player)
    {
    	$this->table = $table;
    	$this->player = $player;
    	$this->active = true;
    	return $this;
    }

    public function getTable()
    {
    	return $this->table;
    }

    public function getPlayer()
    {
    	return $this->player;
    }
    /**
     * Set number
     *
     * @param integer $number
     *
     * @return TournamentNumber
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
    
    public function setName($name)
    {
    	$this->name = $name;
    	return $this;
    }
    
    public function getName()
    {
    	return $this->name;
    }
    
    public function setActive($active = true)
    {
    	$this->active = $active;
    	return $this;
    }
    
    public function isActive()
    {
    	return $this->active;
    }
}
