<?php

namespace AppBundle\Repository;

use Doctrine\ORM\Query\Expr;

/**
 * TournamentRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class TournamentRepository extends \Doctrine\ORM\EntityRepository
{
	/**
	 * Gibt die letzten 5 Hauptevents (Pokernights) vor aktuellem Zeitpunk zurück
	 */
	public function findAnnualRankingRelevant()
	{
		$query = $this->createQueryBuilder('t')
			->where('t.mainTournament = true')
			->andWhere('t.date < CURRENT_TIMESTAMP()')
			->orderBy('t.date','DESC')
			->setMaxResults(5)
			->getQuery();
		
		return $query->getResult();
	}
	
	public function countActivePlayer($tournamentId)
	{
		$query = $this->createQueryBuilder('t')
			->select('COUNT(p.id)')
			->join('t.ranking', 'r', Expr\Join::WITH, 't.id = :id')
			->join('r.player', 'p', Expr\Join::WITH, 'r.rank IS NULL')
			->setParameter('id', $tournamentId)
			->getQuery();
		
		return $query->getSingleScalarResult();
	}
}
