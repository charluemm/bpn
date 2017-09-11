<?php
namespace AppBundle\Model\Player;

use AppBundle\Model\AnnualRanking\AnnualRankingInterface;

interface PlayerInterface
{
    /**
     * Fügt einen 5JW-Eintrag hinzu
     *
     * @param AnnualRankingInterface $annualRanking
     * @return AnnualRankingInterface
     */
    public function addAnnualRanking(AnnualRankingInterface $annualRanking);
    
    /**
     * Entfernt einen 5JW-Eintrag
     *
     * @param AnnualRankingInterface $annualRanking
     * @return AnnualRankingInterface
     */
    public function removeAnnualRanking(AnnualRankingInterface $annualRanking);
    
}