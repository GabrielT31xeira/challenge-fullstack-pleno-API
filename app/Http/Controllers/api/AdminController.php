<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\DashboardResource;
use App\Http\Resources\Order\OrderResource;
use App\Http\Resources\Product\ProductResource;
use App\Http\Resources\Tags\TagsResource;
use App\Services\AdminService;
use App\Services\AuthService;
use App\Support\ApiResponse;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function __construct(
        protected AdminService $adminService
    ){}

    public function dashboard()
    {
        try {
            $data = $this->adminService->dashboard();
            return ApiResponse::success(new DashboardResource($data));
        } catch (\Throwable $th) {
            return ApiResponse::serverError(
                "Erro por parte do servidor, tente novamente mais tarde",
                $th->getMessage());
        }
    }

    public function gelAllTags()
    {
        try {
            $data = $this->adminService->getAllTags();
            return ApiResponse::success(TagsResource::collection($data));
        } catch (\Throwable $th) {
            return ApiResponse::serverError(
                "Erro por parte do servidor, tente novamente mais tarde",
                $th->getMessage());
        }
    }

    public function lowStock()
    {
        try {
            $data = $this->adminService->lowStock();
            $resource = ProductResource::collection($data);

            return ApiResponse::paginated($resource);
        } catch (\Throwable $th) {
            return ApiResponse::serverError(
                "Erro por parte do servidor, tente novamente mais tarde",
                $th->getMessage());
        }
    }

    public function order(Request $request)
    {
        try {
            $data = $this->adminService->orders($request);
            $resource = OrderResource::collection($data);

            return ApiResponse::paginated($resource);
        } catch (\Throwable $th) {
            return ApiResponse::serverError(
                "Erro por parte do servidor, tente novamente mais tarde",
                $th->getMessage());
        }
    }
}
