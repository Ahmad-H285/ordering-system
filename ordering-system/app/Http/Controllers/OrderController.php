<?php

namespace App\Http\Controllers;

use App\Models\Ingredient;
use App\Models\Item;
use App\Models\Order;
use App\Services\ItemService;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * @var OrderService $orderService
     */
    protected $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function create(Request $request)
    {
        $isCreated = $this->orderService->createOrder(
            $request->post('customer_name'),
            $request->post('merchant_name'),
            $request->post('products')
        );

        if ($isCreated) {
            return response()->json([
                'success' => true,
                'message' => 'Order Created Successfully!'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Something wrong happened or stock not sufficient!'
            ]);
        }
    }
}
