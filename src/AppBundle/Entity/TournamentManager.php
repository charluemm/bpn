<?php
namespace AppBundle\Entity;

use AppBundle\Model\AbstractManager;
use AppBundle\Model\Tournament\TournamentInterface;

class TournamentManager extends AbstractManager
{
    public function create()
    {
        return new Tournament();
    }
    
    public function update(TournamentInterface $tournament, $andFlush = true)
    {
        $this->getEntityManager()->persist($tournament);
        if($andFlush)
            $this->getEntityManager()->flush();
        return true;
    }
    
    public function findAll($mainOnly = false)
    {
        $criteria = array();
        if($mainOnly)
            $criteria['mainTournament'] = true;
        
        return $this->getRepository()->findBy($criteria, array('date' => 'DESC'));
    }
        
    public function find($id)
    {
        return $this->getRepository()->find($id);
    }
    
    public function remove($entity)
    {
        $this->getEntityManager()->remove($entity);
        $this->getEntityManager()->flush();
        return true;
    }
    
    public function findPastMainTournaments()
    {
        $date = new \DateTime();
        $date->setDate(2018, 10, 29);
        
        $query = $this->getRepository()->createQueryBuilder('t')
            ->where('t.mainTournament = true')
            ->andWhere('t.date < :now')
            ->setParameter('now', $date)
            ->addOrderBy('t.date', 'DESC')
            ->getQuery();
        
        return $query->getResult();
    }
}