<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\DB;

class OrderService
{
    /**
     * @var ItemService $itemService
     */
    protected $itemService;
    public function __construct(ItemService $itemService)
    {
        $this->itemService = $itemService;
    }

    /**
     * @param string $customerName
     * @param string $merchantName
     * @param array $items
     * @return bool
     */
    public function createOrder(string $customerName, string $merchantName, array $items): bool
    {
        try {
            DB::beginTransaction();
            $order = Order::create([
                'customer_name' => $customerName,
                'merchant_name' => $merchantName,
            ]);

            $this->itemService->attachItemsToOrder($order, $items);
            DB::commit();

            return true;
        } catch (\Exception $error) {
            DB::rollBack();

            return false;
        }
    }
}
