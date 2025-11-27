<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\DashboardResource;
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
}
