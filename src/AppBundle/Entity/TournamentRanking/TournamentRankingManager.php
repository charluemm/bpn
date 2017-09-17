<?php
namespace AppBundle\Entity\TournamentRanking;

use AppBundle\Entity\TournamentRanking;
use AppBundle\Model\Tournament\TournamentInterface;
use AppBundle\Model\Player\PlayerInterface;
use AppBundle\Model\Tournament\TournamentRankingInterface;
use AppBundle\Model\AbstractManager;

class TournamentRankingManager extends AbstractManager
{
    public function create($tournament, $player)
    {
        return new TournamentRanking($tournament, $player);
    }
    
    public function find($id)
    {
        return $this->getRepository()->find($id);
    }
    
    public function findAll()
    {
        return $this->getRepository()->findAll();
    }
    
    public function findByTournament(TournamentInterface $tournament)
    {
        return $this->getRepository()->findOneBy(array('tournament' => $tournament), array('kickedAt' => 'DESC'));
    }
    
    public function findOneByUnique(TournamentInterface $tournament, PlayerInterface $player)
    {
        return $this->getRepository()->findOneBy(array(
                'tournament' => $tournament,
                'player' => $player
        ));
    }
    
    /**
     * aktualisiert Entity und berechnet die Platzierungen des Turniers neu
     * 
     * @param TournamentRankingInterface $ranking
     */
    public function update(TournamentRankingInterface $ranking)
    {
        // update entry        
        if(!$this->getEntityManager()->contains($ranking))
            $this->getEntityManager()->persist($ranking);
        
        $this->getEntityManager()->flush($ranking);
        
        // refresh ranking
        $this->refreshRanking($ranking->getTournament());
        return true;
    }
    
    public function refreshRanking(TournamentInterface $tournament)
    {
        /** @var $rank TournamentRanking **/
        $qb = $this->getRepository()->createQueryBuilder('r');
        $query = $qb
            ->where('r.tournament = :tournament')
            //->andWhere($qb->expr()->isNotNull('r.kickedAt'))
            ->setParameter('tournament', $tournament)
            ->orderBy('r.kickedAt', 'ASC')
            ->getQuery();
        
        $list = $query->getResult();
        $cnt = \count($list);
        
        foreach($list as $index => $rank)
        {
            if($rank->getKickedAt() !== null)
            {
                $rank->setRank($index + 1);
            }
        }
        
        $this->getEntityManager()->flush();
        return true;
    }
}