<?php

namespace AppBundle\Model\Player;

use Doctrine\Common\Collections\ArrayCollection;
use AppBundle\Model\Player\PlayerInterface;
use AppBundle\Model\AnnualRanking\AnnualRankingInterface;
use AppBundle\Entity\TournamentRanking;

/**
 * AbstractPlayer
 *
 */
abstract class AbstractPlayer implements PlayerInterface
{
     /**
     * @var string
     */
    protected $surname;

    /**
     * @var string
     */
    protected $givenname;

    /**
     * @var string
     */
    protected $email;

    /**
     * @var string
     */
    protected $nickname;

    /**
     * @var ArrayCollection
     */
    protected $tournamentRanking;
    
    /**
     * @var ArrayCollection
     */
    protected $annualRanking;
    
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
     * Set surname
     *
     * @param string $surname
     *
     * @return PlayerInterface
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
     * @return PlayerInterface
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
     * @return PlayerInterface
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
     * @return PlayerInterface
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
     * 
     * {@inheritDoc}
     * @see \AppBundle\Model\Player\PlayerInterface::addAnnualRanking()
     */
    public function addAnnualRanking(AnnualRankingInterface $ranking)
    {
    	if(!$this->annualRanking->contains($ranking))
    		$this->annualRanking->add($ranking);
    	return $this;
    }
    
    /**
     * 
     * {@inheritDoc}
     * @see \AppBundle\Model\Player\PlayerInterface::removeAnnualRanking()
     */
    public function removeAnnualRanking(AnnualRankingInterface $ranking)
    {
    	if(!$this->annualRanking->contains($ranking))
    		return false;
    	
   		$this->annualRanking->remove($ranking);
		return true;
    }
    
    public function getAnnualRankingPoints()
    {
        if($this->annualRanking->isEmpty())
            return 0;
        
        return $this->annualRanking->first()->getSumPoints();
    }
}
