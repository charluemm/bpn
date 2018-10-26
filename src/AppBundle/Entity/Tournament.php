<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use AppBundle\Model\Tournament\TournamentInterface;
use AppBundle\Model\Tournament\AbstractTournament;

/**
 * Tournament
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TournamentRepository")
 */
class Tournament extends AbstractTournament
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

     /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    protected $name;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="date")
     */
    protected $date;

    /**
     * @var string
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    protected $description;
    
    /**
     * @var boolean
     * 
     * @ORM\Column(name="is_main", type="boolean", options={"default"=false})
     */
    protected $mainTournament;
    
    /**
     * @var Location
     *
     * @ORM\ManyToOne(targetEntity="Location")
     * @ORM\JoinColumn(name="location_id", referencedColumnName="id")
     **/
    protected $location;
    
    /**
     * @var Event
     * 
     * @ORM\ManyToOne(targetEntity="Event", inversedBy="tournaments")
     * @ORM\JoinColumn(name="event_id", referencedColumnName="id")
     **/
    protected $event;
    
    /**
     * @var ArrayCollection
     *  
     * @ORM\OneToMany(targetEntity="TournamentRanking", mappedBy="tournament") 
     * @ORM\OrderBy({"rank" = "ASC", "player" = "ASC"})     
     */
    protected $ranking;
    
    /**
     * @var ArrayCollection
     *  
     * @ORM\OneToMany(targetEntity="TournamentTable", mappedBy="tournament") 
     * @ORM\OrderBy({"number" = "ASC"})     
     */
    protected $tables;
      
    /**
     * @ORM\ManyToOne(targetEntity="BlindLevel")
     * @ORM\JoinColumn(name="blind_lvl_id", referencedColumnName="id", nullable=true)
     * @var BlindLevel
     */
    protected $blindLevel;
    
    /**
     * @ORM\Column(name="last_blind_raise", type="datetime", nullable=true)
     * @var \DateTime
     */
    protected $lastBlindRaiseAt;
    
    /**
     * @ORM\ManyToOne(targetEntity="TournamentStatus")
     * @ORM\JoinColumn(name="current_status", referencedColumnName="id", nullable=true)
     * @var TournamentStatus
     */
    protected $tournamentStatus;
    
    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }
}
