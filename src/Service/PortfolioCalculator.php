<?php
// src/Service/PortfolioCalculator.php
namespace App\Service;

use App\Entity\Portfolio;

class PortfolioCalculator
{
    
    public function calculateCurrentValue(Portfolio $portfolio): float
    {
        $totalValue = 0.0;

        // Agrupamos las transacciones por ticker para saber cuántas acciones tenemos de cada activo
        $holdings = [];
        foreach ($portfolio->getTransactions() as $transaction) {
            $ticker = $transaction->getTicker();
            if (!isset($holdings[$ticker])) {
                $holdings[$ticker] = 0.0;
            }
            
            if (strtoupper($transaction->getType()) === 'BUY') {
                $holdings[$ticker] += $transaction->getShares();
            }
        }

        foreach ($holdings as $ticker => $shares) {
            $currentPrice = $this->getSimulatedCurrentPrice($ticker);
            $totalValue += $shares * $currentPrice;
        }

        return $totalValue;
    }


    private function getSimulatedCurrentPrice(string $ticker): float
    {
        return match ($ticker) {
            'VWCE.DE' => 115.70, 
            'AGGH.AS' => 5.05,  
            default => 100.0,   
        };
    }
}