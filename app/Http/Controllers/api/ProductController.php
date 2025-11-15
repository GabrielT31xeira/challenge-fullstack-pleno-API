<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Product\ProductStoreRequest;
use App\Http\Requests\Product\ProductUpdateRequest;
use App\Services\ProductService;

class ProductController extends Controller
{
    public function __construct(
        protected ProductService $products
    ) {}

    public function index()
    {
        return response()->json(
            $this->products->list(request()->all())
        );
    }

    public function show($id)
    {
        return response()->json(
            $this->products->show($id)
        );
    }

    public function store(ProductStoreRequest $request)
    {
        return response()->json(
            $this->products->create($request->validated()),
            201
        );
    }

    public function update(ProductUpdateRequest $request, $id)
    {
        return response()->json(
            $this->products->update($id, $request->validated())
        );
    }

    public function destroy($id)
    {
        $this->products->delete($id);

        return response()->json(['message' => 'Produto removido com sucesso.']);
    }
}
