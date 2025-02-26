<?php

declare(strict_types=1);

namespace App\Endpoint\Web;

use Exception;
use Spiral\Prototype\Traits\PrototypeTrait;
use Spiral\Router\Annotation\Route;
use App\Service\DatabaseSeeder;
use App\Service\ReportService;

/**
 * Simple home page controller. It renders home page template and also provides
 * an example of exception page.
 */
final class HomeController
{
    /**
     * Read more about Prototyping:
     * @link https://spiral.dev/docs/basics-prototype/#installation
     */
    use PrototypeTrait;


    #[Route(route: '/', name: 'index')]
    public function index(): string
    {
        return $this->views->render('home');
    }

    /**
     * Example of exception page.
     */
    #[Route(route: '/exception', name: 'exception')]
    public function exception(): never
    {
        throw new Exception('This is a test exception.');
    }

    /**
     * Database Seeder Route
     */
    #[Route(route: '/populate-db', name: 'populate-db', methods: 'GET')]
    public function populateDb(DatabaseSeeder $seeder): \Psr\Http\Message\ResponseInterface
    {
        $seeder->run();

        return $this->response->json(['message' => 'Database populated successfully.']);
    }

    #[Route(route: '/monthly-sales-by-region', methods: ['GET'])]
    public function monthlySalesByRegion(ReportService $reportService): \Psr\Http\Message\ResponseInterface
    {
        return $this->response->json($reportService->getMonthlySalesByRegion());
    }

    #[Route(route: '/top-categories-by-store', methods: ['GET'])]
    public function topCategoriesByStore(ReportService $reportService): \Psr\Http\Message\ResponseInterface
    {
        $startDate = $_GET['start_date'] ?? date('Y-m-d', strtotime('-3 months'));
        $endDate = $_GET['end_date'] ?? date('Y-m-d');

        return $this->response->json($reportService->getTopCategoriesByStore($startDate, $endDate));
    }
    
}
