<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class ProductUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'         => 'sometimes|string|max:255',
            'description'  => 'nullable|string',
            'price'        => 'sometimes|numeric|min:0',
            'cost_price'   => 'sometimes|numeric|min:0',
            'quantity'     => 'sometimes|integer|min:0',
            'min_quantity' => 'sometimes|integer|min:0',
            'active'       => 'boolean',
            'category_id'  => 'nullable|uuid|exists:categories,id',
            'tags'         => 'array',
            'tags.*'       => 'uuid|exists:tags,id',
        ];
    }

    public function messages()
    {
        return [
            'name.string'           => 'O nome do produto deve ser um texto válido.',
            'name.max'              => 'O nome do produto não pode ultrapassar 255 caracteres.',

            'description.string'    => 'A descrição deve ser um texto válido.',

            'price.numeric'         => 'O preço deve ser um número válido.',
            'price.min'             => 'O preço não pode ser negativo.',

            'cost_price.numeric'    => 'O preço de custo deve ser um número válido.',
            'cost_price.min'        => 'O preço de custo não pode ser negativo.',

            'quantity.integer'      => 'A quantidade deve ser um número inteiro.',
            'quantity.min'          => 'A quantidade não pode ser negativa.',

            'min_quantity.integer'  => 'A quantidade mínima deve ser um número inteiro.',
            'min_quantity.min'      => 'A quantidade mínima não pode ser negativa.',

            'active.boolean'        => 'O campo "ativo" deve ser verdadeiro ou falso.',

            'category_id.uuid'      => 'O ID da categoria deve ser um UUID válido.',
            'category_id.exists'    => 'A categoria selecionada não existe.',

            'tags.array'            => 'A lista de tags deve ser um array.',
            'tags.*.uuid'           => 'O ID de cada tag deve ser um UUID válido.',
            'tags.*.exists'         => 'Uma ou mais tags fornecidas não existem.',
        ];
    }

}
