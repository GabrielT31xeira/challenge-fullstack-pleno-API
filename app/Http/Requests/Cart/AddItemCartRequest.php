<?php

namespace App\Http\Requests\Cart;

use Illuminate\Foundation\Http\FormRequest;

class AddItemCartRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'cart_id' => 'nullable|uuid|exists:carts,id',
            'product_id' => 'required|string|exists:products,id',
            'quantity' => 'required|integer|min:1'
        ];
    }

    public function messages(): array
    {
        return [
            'cart_id.uuid'   => 'O ID do carrinho informado é inválido.',
            'cart_id.exists' => 'O carrinho selecionado não foi encontrado.',
            
            'product_id.required' => 'O campo product_id é obrigatório.',
            'product_id.string'   => 'O product_id deve ser uma string válida.',
            'product_id.exists'   => 'O produto informado não foi encontrado.',

            'quantity.required' => 'A quantidade é obrigatória.',
            'quantity.integer'  => 'A quantidade deve ser um número inteiro.',
            'quantity.min'      => 'A quantidade mínima permitida é 1.',
        ];
    }
}
