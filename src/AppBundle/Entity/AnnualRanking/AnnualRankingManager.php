<?php
namespace AppBundle\Entity\AnnualRanking;

use AppBundle\Model\Player\PlayerInterface;
use AppBundle\Model\Tournament\TournamentInterface;
use AppBundle\Model\AnnualRanking\AnnualRankingInterface;
use AppBundle\Model\AbstractManager;
use AppBundle\Entity\Tournament;

class AnnualRankingManager extends AbstractManager
{  
    public function findAll()
    {
        return $this->getRepository()->findAll();
        
    }
    
    /**
     * Legt eine neue Instanz an
     * 
     * @param PlayerInterface $player
     * @param TournamentInterface $tournament
     * @return \AppBundle\Entity\AnnualRanking\AnnualRanking
     */
    public function create(PlayerInterface $player = null, TournamentInterface $tournament = null)
    {
        return new AnnualRanking($player, $tournament);
    }
    
    /**
     * 
     * @param AnnualRankingInterface $annualRanking
     * @param bool $andFlush
     * @return boolean
     */
    public function update(AnnualRankingInterface $annualRanking, $andFlush = true)
    {
        $this->getEntityManager()->persist($annualRanking);
        if($andFlush)
        {
            $this->getEntityManager()->flush();
        }
        return true;
    }
    
    public function find($id)
    {
        return $this->getRepository()->find($id);
    }
    
    public function removeAll()
    {
        $query = $this->getRepository()->createQueryBuilder('r')
            ->delete()
            ->getQuery();
        
        return $query->getResult();
    }
    
    public function findCurrentRanking()
    {
        $query = $this->getRepository()->createQueryBuilder('r')
        ->join('r.tournament', 't')
        ->groupBy('r.player')
        ->having('t.date = MAX(t.date)')
        ->orderBy('r.sumPoints', 'DESC')
        ->getQuery();
        
        return $query->getResult();
    }
    
    public function findCurrentForTournament(Tournament $tournament)
    {
        $year = $tournament->getDate()->format('Y') - 1;
        
        $query = $this->getRepository()->createQueryBuilder('r')
            ->join('r.tournament', 't')
            ->where('r.year = :year')
            ->setParameter('year', $year)
            ->andWhere('t.date <= :maxDate')
            ->setParameter('maxDate', $tournament->getDate())
            ->andWhere('r.player IN (:players)')
            ->setParameter('players', $tournament->getPlayers())
            ->groupBy('r.player')
            ->having('t.date = MAX(t.date)')
            ->orderBy('r.sumPoints', 'DESC')
            ->getQuery();
            
        return $query->getResult();
    }
    
    protected function getEntityManager()
    {
        return $this->_em;
    }
    
    protected function getRepository()
    {
        return $this->_repo;
    }
}