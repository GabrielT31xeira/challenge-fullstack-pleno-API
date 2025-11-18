<?php

namespace App\Http\Controllers\api;

use App\DTO\Order\CreateOrderDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Order\OrderStoreRequest;
use App\Http\Requests\Order\OrderUpdateRequest;
use App\Http\Resources\Order\OrderResource;
use App\Models\Cart;
use App\Services\CartService;
use Illuminate\Http\Request;
use App\Services\OrderService;

class OrderController extends Controller
{
    protected $orderService;
    protected $cartService;
    public function __construct(OrderService $orderService, CartService $cartService)
    {
        $this->orderService = $orderService;
        $this->cartService = $cartService;
    }

    public function index(Request $request)
    {
        try {
            $orders = $this->orderService->listByUser($request);

            return OrderResource::collection($orders);

        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 422);
        }
    }


    public function show($id)
    {
        try {
            return $this->orderService->getOne($id);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 422);
        }
    }

    public function store(OrderStoreRequest $request)
    {
        try {
            $dto = CreateOrderDTO::fromRequest($request, $request->user());
            $order = $this->orderService->createOrder($dto);

            return new OrderResource($order);

        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 422);
        }
    }


    public function updateStatus(OrderUpdateRequest $request, string $id)
    {
        try {
            $status = $request->input('status');

            return $this->orderService->updateStatus($id, $status);

        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 422);
        }
    }
}
