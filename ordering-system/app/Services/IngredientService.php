<?php

namespace App\Services;

use App\Mail\StockNotification;
use App\Models\Ingredient;
use App\Models\Item;
use Illuminate\Support\Facades\Mail;
use mysql_xdevapi\Exception;

class IngredientService
{
    /**
     * @param Item $item
     * @param $quantity
     * @return void
     */
    public function updateCurrentStock(Item $item, $quantity): void
    {
        /** @var Ingredient $ingredient */
        foreach ($item->ingredients as $ingredient) {
            $totalIngredientGrams = $ingredient->pivot->weight * $quantity;
            $ingredientNewStock = $ingredient->current_stock - $totalIngredientGrams;
            $remainingStockPercentage = ($ingredientNewStock * 100) / $ingredient->max_stock;

            if ($ingredientNewStock < 0) {
                throw new Exception("Insufficient Stock For Item: " . $item->name);
            }

            if ($remainingStockPercentage < 50 && !$ingredient->is_notified) {
                Mail::to('admin@foodics.com')
                    ->send(new StockNotification($ingredient));
                $ingredient->is_notified = true;
            }

            $ingredient->current_stock = $ingredientNewStock;
            $ingredient->save();
        }
    }
}
