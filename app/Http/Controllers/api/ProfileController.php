<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Auth\UserResource;
use App\Support\ApiResponse;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        try {
            return ApiResponse::success(new UserResource($request->user()));
        } catch (\Throwable $th) {
            return ApiResponse::serverError(
                "Erro por parte do servidor, tente novamente mais tarde",
                $th->getMessage());
        }

    }
}
