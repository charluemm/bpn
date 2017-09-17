<?php
namespace AppBundle\Model\Tournament;


use AppBundle\Model\Player\PlayerInterface;

interface TournamentRankingInterface
{
    /**
     * @return TournamentInterface
     */
    public function getTournament();
    
    /**
     * @return PlayerInterface
     */
    public function getPlayer();
}