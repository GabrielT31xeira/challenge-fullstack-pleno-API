<?php

namespace App\Http\Controllers\api;

use App\DTO\Order\CreateOrderDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Order\OrderStoreRequest;
use App\Http\Requests\Order\OrderUpdateRequest;
use App\Http\Resources\Order\OrderResource;
use App\Services\CartService;
use App\Support\ApiResponse;
use Illuminate\Http\Request;
use App\Services\OrderService;
use Nette\Schema\ValidationException;

class OrderController extends Controller
{
    public function __construct(
        protected OrderService $orderService,
        protected CartService $cartService
    ){}

    public function index(Request $request)
    {
        try {
            $orders = $this->orderService->listByUser($request);
            $resource = OrderResource::collection($orders);
            return ApiResponse::paginated($resource);
        } catch (\Throwable $th) {
            return ApiResponse::serverError(
                "Erro ao carregar ordens de pedido",
                $th->getMessage()
            );
        }
    }


    public function show($id)
    {
        try {
            $order = $this->orderService->getOne($id);
            $resource = new OrderResource($order);
            return ApiResponse::success($resource);
        } catch (\Throwable $th) {
            return ApiResponse::serverError(
                "Erro ao buscar ordens de pedido",
                $th->getMessage()
            );
        }
    }

    public function store(OrderStoreRequest $request)
    {
        try {
            $dto = CreateOrderDTO::fromRequest($request, $request->user());
            $order = $this->orderService->createOrder($dto);
            return ApiResponse::success(new OrderResource($order));
        } catch (ValidationException $e) {
            return ApiResponse::error($e->errors());
        }
        catch (\Throwable $th) {
            return ApiResponse::serverError(
                "Erro ao criar pedido",
                $th->getMessage()
            );
        }
    }


    public function updateStatus(OrderUpdateRequest $request, string $id)
    {
        try {
            $order = $this->orderService->updateStatus($id, $request->input('status'));
            return ApiResponse::success(new OrderResource($order));
        }catch (ValidationException $e) {
            return ApiResponse::error($e->errors());
        }
        catch (\Throwable $th) {
            return ApiResponse::serverError(
                "Erro ao atualizar ordens de pedido",
                $th->getMessage()
            );
        }
    }
}
