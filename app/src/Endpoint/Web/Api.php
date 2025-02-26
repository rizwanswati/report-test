<?php

declare(strict_types=1);

namespace App\Endpoint\Web;

use App\Entity\Order;
use Cycle\ORM\ORMInterface;
use Spiral\Http\ResponseWrapper;
use Spiral\Router\Annotation\Route;
use App\Service\ReportService;
use App\Service\DatabaseSeeder;

final class Api
{
    private ResponseWrapper $response;
    
    public function __construct(ResponseWrapper $response)
    {
        $this->response = $response;
    }

    #[Route(route: '/populate-db', name: 'populate-db', methods: 'GET')]
    public function populateDb(\App\Service\DatabaseSeeder $seeder): \Psr\Http\Message\ResponseInterface
    {
        $seeder->run();
        
        return $this->response->json(['message' => 'Database populated successfully.']);
    }
    
    #[Route(route: '/show-orders', name: 'show-orders', methods: 'GET')]
    public function showOrders(ORMInterface $orm): \Psr\Http\Message\ResponseInterface
    {
        $repository = $orm->getRepository(Order::class);
        
        $orders = $repository->findAll();
        $data = array_map(fn(Order $order) => $order->toArray(), $orders);
        
        return $this->response->json(['data' => $data]);
    }
   
    #[Route(route: '/monthly-sales-by-region', methods: ['GET'])]
    public function monthlySalesByRegion(ReportService $reportService): \Spiral\Http\Response
    {
        return $this->response->json($this->reportService->getMonthlySalesByRegion());
    }

    #[Route(route: '/top-categories-by-store', methods: ['GET'])]
    public function topCategoriesByStore(ReportService $reportService): \Spiral\Http\Response
    {
        $startDate = $_GET['start_date'] ?? date('Y-m-d', strtotime('-3 months'));
        $endDate = $_GET['end_date'] ?? date('Y-m-d');

        return $this->response->json($this->reportService->getTopCategoriesByStore($startDate, $endDate));
    }
    
}