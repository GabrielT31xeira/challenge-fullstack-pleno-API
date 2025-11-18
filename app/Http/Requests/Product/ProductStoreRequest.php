<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class ProductStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules()
    {
        return [
            'name'         => 'required|string|max:255|unique:products,name',
            'slug'         => 'nullable|string',
            'description'  => 'nullable|string',
            'price'        => 'required|numeric|min:0.01',
            'cost_price'   => 'nullable|numeric|lt:price',
            'quantity'     => 'required|integer|min:0',
            'min_quantity' => 'nullable|integer|min:0',
            'active'       => 'boolean',
            'category_id'  => 'required|exists:categories,id'
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'O nome é obrigatório.',
            'name.unique' => 'Já existe um produto com este nome.',
            'price.required' => 'O preço é obrigatório.',
            'price.min' => 'O preço deve ser maior que zero.',
            'cost_price.lt' => 'O custo deve ser menor que o preço de venda.',
            'quantity.min' => 'A quantidade não pode ser negativa.',
            'category_id.exists' => 'A categoria informada não existe.',
        ];
    }

}
