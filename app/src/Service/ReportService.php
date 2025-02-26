<?php

declare(strict_types=1);

namespace App\Service;

use Cycle\ORM\ORMInterface;
use Cycle\ORM\Select;
use Cycle\Database\Injection\Fragment;

final class ReportService
{
    private ORMInterface $orm;

    public function __construct(ORMInterface $orm)
    {
        $this->orm = $orm;
    }

    /**
     * Get Monthly Sales by Region
     */
    public function getMonthlySalesByRegion(): array
    {
    $query = (new Select($this->orm, 'order'))
        ->columns([
            'year' => new Fragment('YEAR(order_date)'),
            'month' => new Fragment('MONTH(order_date)'),
            'region_id' => 'store.region_id',
            'total_sales' => new Fragment('SUM(unit_price * quantity)'),
            'total_orders' => new Fragment('COUNT(order_id)'),
        ])
        ->load('store')
        ->groupBy(new Fragment('YEAR(order_date)'))
        ->groupBy(new Fragment('MONTH(order_date)'))
        ->groupBy('store.region_id')
        ->orderBy(new Fragment('YEAR(order_date)'), 'DESC')  // 
        ->orderBy(new Fragment('MONTH(order_date)'), 'DESC'); // 

    return $query->fetchAll();
    }
    /**
     * Get Top Categories by Store for a given date range
     */
    public function getTopCategoriesByStore(string $startDate, string $endDate): array
    {
    $query = (new Select($this->orm, 'order'))
        ->columns([
            'store_id' => 'store.store_id',
            'category_id' => 'product.category_id',
            'total_sales' => new Fragment('SUM(unit_price * quantity)'),
            'rank_within_store' => new Fragment(
                'RANK() OVER (PARTITION BY store.store_id ORDER BY SUM(unit_price * quantity) DESC)'
            ),
        ])
        ->load(['store', 'product'])
        ->where('orderDate', '>=', $startDate)
        ->where('orderDate', '<=', $endDate)
        ->groupBy('store.store_id')
        ->groupBy('product.category_id');

    return $query->fetchAll();
    }
}
