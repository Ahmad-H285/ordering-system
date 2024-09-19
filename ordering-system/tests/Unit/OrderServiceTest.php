<?php

namespace Tests\Unit;

use App\Models\Item;
use App\Models\Order;
use App\Services\IngredientService;
use App\Services\ItemService;
use App\Services\OrderService;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\TestCase;
use Mockery;

class OrderServiceTest extends TestCase
{
    use RefreshDatabase;
    use DatabaseMigrations;
    use DatabaseMigrations;

    protected $itemServiceMock;
    protected $ingredientServiceMock;
    protected $orderService;
    protected $itemService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->itemServiceMock = Mockery::mock(ItemService::class);
        $this->ingredientServiceMock = Mockery::mock(IngredientService::class);

        $this->orderService = new OrderService($this->itemServiceMock);
        $this->itemService = new ItemService($this->ingredientServiceMock);
    }

    /** @test */
    public function create_order_test()
    {
        $orderData = [
            'customer_name' => 'Ahmad Youssef',
            "merchant_name" => "Merchant name",
            'products' => [
                ['id' => 1, 'quantity' => 2],
                ['id' => 2, 'quantity' => 1]
            ]
        ];

        $order = Order::create([
            'customer_name' => $orderData['customer_name'],
            'merchant_name' => $orderData['merchant_name'],
        ]);

        $this->itemServiceMock
            ->shouldReceive('attachItemsToOrder')
            ->with($order, $orderData['products'])
            ->once();

        $isOrderCreated = $this->orderService->createOrder(
            $orderData['customer_name'],
            $orderData['merchant_name'],
            $orderData['products']
        );

        $this->assertInstanceOf(Order::class, $order);
        $this->assertTrue($isOrderCreated);
    }

    /** @test */
    public function attach_order_items_test()
    {
        $items = [
            ['id' => 1, 'quantity' => 2],
        ];

        $order = Order::create([
            'customer_name' => 'Ahmad Youssef',
            'merchant_name' => 'Merchant name',
        ]);

        $item = Item::find($items[0]['id']);

        $this->ingredientServiceMock
            ->shouldReceive('updateCurrentStock')
            ->with($item, $items[0]['quantity'])
            ->once();

        $this->itemService->attachItemsToOrder($order, $items);

        $this->assertDatabaseHas('order_item', [
            'order_id' => $order->id,
            'item_id' => $item->id,
            'quantity' => $items[0]['quantity']
        ]);
    }
}
