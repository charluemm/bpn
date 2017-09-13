<?php
namespace AppBundle\Entity\AnnualRanking;

use AppBundle\Model\Player\PlayerInterface;
use AppBundle\Model\Tournament\TournamentInterface;
use AppBundle\Model\AnnualRanking\AnnualRankingInterface;
use AppBundle\Model\AbstractManager;
use AppBundle\Entity\Tournament;
use Doctrine\ORM\Query\Expr\Select;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Common\Util\Debug;
use Doctrine\ORM\Query\Expr;

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
    
    public function findAdvancedForTournament(Tournament $tournament = null)
    {
        if(is_null($tournament))
        {
            $subQb = $this->getEntityManager()->getRepository('AppBundle:Tournament')->createQueryBuilder('t');
            $subQuery = $subQb
                ->join('AppBundle:AnnualRanking\AnnualRanking', 'rt', Expr\Join::WITH, 't.id = rt.tournament')
                ->groupBy('t.id')
                ->orderBy('t.date', 'DESC')
                ->setMaxResults(2)
                ->getQuery();
        }
        else 
        {   
            $subQb = $this->getEntityManager()->getRepository('AppBundle:Tournament')->createQueryBuilder('t');
            // die letzten beiden Turniere (main) holen
            $subQuery = $subQb
            ->where('t.date < :currDate')
            ->setParameter('currDate', $tournament->getDate())
            ->andWhere('t.mainTournament = true')
            ->andWhere('t.id != :self')
            ->setParameter('self', $tournament)
            ->orderBy('t.date', 'DESC')
            ->setMaxResults(2)
            ->getQuery();
        }
        
        // main QUERY
        $qb = $this->getRepository()->createQueryBuilder('r');
        $qb
            ->select('p.nickname as player')
            ->join('r.player', 'p')
            ->join('r.tournament', 't')
            //->where($qb->expr()->in('r.tournament', $subQuery->getDQL()))
            ->orderBy('curr_rank', 'ASC')
            ->groupBy('r.player');
        
        // filter player if tournament is set
        if(!is_null($tournament))
        {
            $qb->andWhere('r.player IN (:players)')->setParameter('players', $tournament->getPlayers());  
        }
        
        // add SELECT for each SUBQUERY
        foreach ($subQuery->getResult() as $key => $value)
        {
            $subRankQb = $this->getRepository()->createQueryBuilder("rt$key");
            $subRankQuery = $subRankQb
                ->select("COUNT(rt$key.id) + 1")
                ->where("rt$key.tournament = :tournament$key")
                ->andWhere($subRankQb->expr()->gt("rt$key.sumPoints", "(".
                        $this->sumDQL($key)
                    .")"))
                ->getQuery();
                        
            $qb
                ->setParameter("tournament$key", $value)
                ->addSelect('('.$this->sumDQL($key).') AS '.($key == 0 ? "current" : "previous"))
                ->addSelect('('.$subRankQuery->getDQL().') AS '.($key == 0 ? "curr_rank" : "prev_rank"));
        }
        
        $query = $qb->getQuery();
        
        return $query->getArrayResult();
    }
    
    private function sumDQL($key) 
    {
        $alias = "alias_".rand(0, 1024);
        
        return $this->getRepository()->createQueryBuilder($alias)
            ->select("$alias.sumPoints")
            ->where("$alias.tournament = :tournament$key")
            ->andWhere("$alias.player = IDENTITY(r.player)")
            ->getDQL();
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