<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Store;
use App\Entity\Product;
use App\Entity\Order;
use Cycle\ORM\EntityManager;
use Cycle\ORM\ORM;
use Faker\Factory;

class DatabaseSeeder
{
    private ORM $orm;

    public function __construct(ORM $orm)
    {
        $this->orm = $orm;
    }

    public function run(): void
{
    $faker = \Faker\Factory::create();
    $em = new EntityManager($this->orm);

    //  Insert Stores First
    $stores = [];
    for ($i = 0; $i < 5; $i++) {
        $store = new Store($faker->numberBetween(1, 10), $faker->company);
        $em->persist($store);
        $stores[] = $store;
    }
    $em->run(); // Save Stores

    //  Insert Products
    $products = [];
    for ($i = 0; $i < 10; $i++) {
        $product = new Product($faker->numberBetween(1, 5), $faker->word);
        $em->persist($product);
        $products[] = $product;
    }
    $em->run(); // Save Products before Orders

    //  Insert Orders (Fetching Store & Product from ORM)
    for ($i = 0; $i < 20; $i++) {
        $store = $stores[array_rand($stores)];
        $product = $products[array_rand($products)];

        // Ensure product exists before using its ID
        if ($product === null || $store === null) {
            continue;
        }

        $order = new Order(
            $faker->numberBetween(1, 50), // Random customer ID
            $product->getProductId(), //  Ensure this exists
            $faker->numberBetween(1, 5),
            $faker->randomFloat(2, 10, 500),
            $faker->dateTimeThisYear,
            $store->getStoreId() //  Ensure this exists
        );
        $order->store = $store; // Assign Store
        $order->product = $product; //  Assign Product
        $em->persist($order);
    }
    $em->run(); // Save Orders
}
}
