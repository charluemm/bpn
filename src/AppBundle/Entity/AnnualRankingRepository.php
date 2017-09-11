<?php

namespace AppBundle\Entity;

/**
 * AnnualRankingRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class AnnualRankingRepository extends \Doctrine\ORM\EntityRepository
{
    public function findLastGroupByPlayer()
    {
        $query = $this->createQueryBuilder('a')
        ->join('a.tournament', 't')
        ->groupBy('a.player')
        ->having('t.date =  MAX(t.date)')
        ->orderBy('a.sumPoints', 'DESC')
        ->getQuery();
        
        return $query->getResult();
    }
    /**
     * Gibt Jahres
     * @param Tournament $tournament
     * @return array
     */
    public function findByTournament(Tournament $tournament)
    {
        $query = $this->createQueryBuilder('a')
            ->where('a.tournament = :tournament')
            ->setParameter('tournament', $tournament)
            ->orderBy('a.sumPoints', 'DESC')
            ->getQuery();
        
        return $query->getResult();
    }
    
    public function findLastByPlayerList(array $playerList)
    {
        $query = $this->createQueryBuilder('a')
        ->join('a.tournament', 't')
        ->where('a.player IN (:listPlayer)')
        ->setParameter('listPlayer', $playerList)
        ->having('t.date =  MAX(t.date)')
        ->addGroupBy('a.player')
        ->addOrderBy('a.sumPoints', 'DESC')
        ->getQuery();
        
        return $query->getResult();
    }
}
