<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use AppBundle\Model\Player\AbstractPlayer;

/**
 * Player
 *
 * @ORM\Table(name="app_player")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PlayerRepository")
 */
class Player extends AbstractPlayer
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
     * @ORM\Column(name="surname", type="string", length=255)
     */
    protected $surname;

    /**
     * @var string
     *
     * @ORM\Column(name="givenname", type="string", length=255)
     */
    protected $givenname;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, nullable=true)
     */
    protected $email;

    /**
     * @var string
     *
     * @ORM\Column(name="nickname", type="string", length=255)
     */
    protected $nickname;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="TournamentRanking", mappedBy="player")
     * @ORM\OrderBy({"tournament" = "ASC"})
     */
    protected $tournamentRanking;
    
    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\AnnualRanking\AnnualRanking", mappedBy="player")
     * @ORM\OrderBy({"year" = "DESC"})
     */
    protected $annualRanking;
        
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
