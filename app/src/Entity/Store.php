<?php

declare(strict_types=1);

namespace App\Entity;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Relation\HasMany;

#[Entity(table: 'stores')]
class Store
{
    #[Column(type: 'bigPrimary')]
    private int $storeId;
    
    #[Column(type: 'bigInteger')]
    private int $regionId;
    
    #[Column(type: 'string', length: 200)]
    private string $storeName;
    
    #[HasMany(target: Order::class)]
    private array $orders;

    public function __construct(int $regionId, string $storeName)
    {
        $this->regionId = $regionId;
        $this->storeName = $storeName;
    }

    public function getStoreId(): int
    {
        return $this->storeId;
    }
}