<?php

namespace App\Services;

use App\Models\Ingredient;
use App\Models\Item;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

class ItemService
{
    /**
     * @var IngredientService $ingredientService
     */
    protected $ingredientService;
    public function __construct(IngredientService $ingredientService)
    {
        $this->ingredientService = $ingredientService;
    }

    /**
     * @param Order $order
     * @param array $items
     * @return void
     */
    public function attachItemsToOrder(Order $order, array $items): void
    {
        foreach ($items as $requestItem) {
            /** @var Item $item */
            $item = Item::find($requestItem['id']);
            if ($item) {
                $order->items()->attach(
                    $item->id, [
                    'quantity' => $requestItem['quantity'],
                ]);

                // Update item ingredients stock
                $this->ingredientService->updateCurrentStock($item, $requestItem['quantity']);
            }
        }
    }
}
