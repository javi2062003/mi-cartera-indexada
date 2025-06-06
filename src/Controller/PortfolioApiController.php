<?php

namespace App\Controller;

use App\Entity\Portfolio;
use App\Service\PortfolioCalculator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api')]
class PortfolioApiController extends AbstractController
{
    public function __construct(
        private readonly PortfolioCalculator $portfolioCalculator
    ) {
    }

    #[Route('/portfolio/{id}', name: 'api_get_portfolio_details', methods: ['GET'])]
    public function getPortfolioDetails(Portfolio $portfolio): JsonResponse
    {
        $currentValue = $this->portfolioCalculator->calculateCurrentValue($portfolio);

        $transactionsData = [];
        foreach ($portfolio->getTransactions() as $transaction) {
            $transactionsData[] = [
                'ticker' => $transaction->getTicker(),
                'assetName' => $transaction->getAssetName(),
                'type' => $transaction->getType(),
                'shares' => $transaction->getShares(),
                'price' => $transaction->getPrice(),
                'date' => $transaction->getDate()->format('Y-m-d H:i:s'),
            ];
        }

        $responseData = [
            'portfolio_id' => $portfolio->getId(),
            'portfolio_name' => $portfolio->getName(),
            'owner_name' => $portfolio->getOwner()->getFullName(),
            'current_market_value' => round($currentValue, 2),
            'transactions' => $transactionsData,
        ];

        return $this->json($responseData);
    }
}