<?php

declare(strict_types=1);

namespace App\Entity;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Relation\HasMany;

#[Entity(table: 'products')]
class Product
{
    #[Column(type: 'bigPrimary')]
    public int $productId;
    
    #[Column(type: 'bigInteger')]
    private int $categoryId;
    
    #[Column(type: 'string', length: 200)]
    private string $productName;

    public function __construct(int $categoryId, string $productName)
    {
        $this->categoryId = $categoryId;
        $this->productName = $productName;
    }

    public function getProductId(): int
    {
        return $this->productId;
    }
}