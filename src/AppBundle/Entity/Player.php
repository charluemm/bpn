<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Player
 *
 * @ORM\Table(name="app_player")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\PlayerRepository")
 */
class Player
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
     * @ORM\Column(name="surname", type="string", length=255)
     */
    private $surname;

    /**
     * @var string
     *
     * @ORM\Column(name="givenname", type="string", length=255)
     */
    private $givenname;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, nullable=true)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="nickname", type="string", length=255)
     */
    private $nickname;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="TournamentRanking", mappedBy="player")
     * @ORM\OrderBy({"tournament" = "ASC"})
     */
    private $tournamentRanking;
    
    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="AnnualRanking", mappedBy="player")
     * @ORM\OrderBy({"tournament" = "DESC"})
     */
    private $annualRanking;
    
    public function __construct()
    {
    	$this->tournamentRanking = new ArrayCollection();
    	$this->annualRanking = new ArrayCollection();
    	return $this;
    }
    
    public function __toString()
    {
    	return $this->getGivenname().' '.$this->getSurname().' ('.$this->getNickname().')';
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
     * Set surname
     *
     * @param string $surname
     *
     * @return Player
     */
    public function setSurname($surname)
    {
        $this->surname = $surname;

        return $this;
    }

    /**
     * Get surname
     *
     * @return string
     */
    public function getSurname()
    {
        return $this->surname;
    }

    /**
     * Set givenname
     *
     * @param string $givenname
     *
     * @return Player
     */
    public function setGivenname($givenname)
    {
        $this->givenname = $givenname;

        return $this;
    }

    /**
     * Get givenname
     *
     * @return string
     */
    public function getGivenname()
    {
        return $this->givenname;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return Player
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set nickname
     *
     * @param string $nickname
     *
     * @return Player
     */
    public function setNickname($nickname)
    {
        $this->nickname = $nickname;

        return $this;
    }

    /**
     * Get nickname
     *
     * @return string
     */
    public function getNickname()
    {
        return $this->nickname;
    }

    /**
     * Get tournamentRanking
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTournamentRanking()
    {
        return $this->tournamentRanking;
    }
    
    public function addTournamentRank(TournamentRanking $tournamentRank)
    {
    	if($this->tournamentRanking->contains($tournamentRank))
    		return ;
    	
    	$this->tournamentRanking->add($tournamentRank);
    	return $this;
    }

    public function removeTournamentRank(TournamentRanking $tournamentRank)
    {
    	if(!$this->tournamentRanking->contains($tournamentRank))
    		return false;
    	
    	return $this->tournamentRanking->removeElement($tournamentRank);
    }
    
    public function getAnnualRanking()
    {
    	return $this->annualRanking;
    }
    
    /**
     * @param AnnualRanking $ranking
     * @return \AppBundle\Entity\Player
     */
    public function addAnnualRanking(AnnualRanking $ranking)
    {
    	if(!$this->annualRanking->contains($ranking))
    		$this->annualRanking->add($ranking);
    	return $this;
    }
    
    public function removeAnnualRanking(AnnualRanking $ranking)
    {
    	if(!$this->annualRanking->contains($ranking))
    		return false;
    	
   		$this->annualRanking->remove($ranking);
		return true;
    }
    
    public function getAnnualRankingPoints()
    {
    	$sum = 0;
    	/* @var $annualRanking AnnualRanking */
    	foreach($this->getAnnualRanking() as $annualRanking)
    	{
    		$sum += $annualRanking->getPoints();
    	}
    	return $sum;
    }
}
