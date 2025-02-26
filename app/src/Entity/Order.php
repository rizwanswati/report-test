<?php

declare(strict_types=1);

namespace App\Entity;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Relation\BelongsTo;

#[Entity(table: 'orders')]
class Order
{
    #[Column(type: 'bigPrimary')]
    public int $orderId;
    
    #[Column(type: 'bigInteger')]
    public int $customerId;
    
    #[Column(type: 'bigInteger')]
    public int $productId;
    
    #[Column(type: 'integer')]
    public int $quantity;
    
    #[Column(type: 'decimal', precision: 10, scale: 2)]
    public float $unitPrice;
    
    #[Column(type: 'date')]
    public \DateTimeInterface $orderDate;
    
    #[Column(type: 'bigInteger')]
    public int $storeId;

    // ✅ Make relations nullable to avoid infinite loop
    #[BelongsTo(target: Store::class, fkAction: 'CASCADE', nullable: true)]
    public ?Store $store = null;

    #[BelongsTo(target: Product::class, fkAction: 'CASCADE', nullable: true)]
    public ?Product $product = null;

    public function __construct(int $customerId, int $productId, int $quantity, float $unitPrice, \DateTimeInterface $orderDate, int $storeId)
    {
        $this->customerId = $customerId;
        $this->productId = $productId;
        $this->quantity = $quantity;
        $this->unitPrice = $unitPrice;
        $this->orderDate = $orderDate;
        $this->storeId = $storeId;
    }
}