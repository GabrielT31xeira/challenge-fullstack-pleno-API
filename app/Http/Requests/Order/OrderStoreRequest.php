<?php

namespace App\Http\Requests\Order;

use Illuminate\Foundation\Http\FormRequest;

class OrderStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'cart_id' => ['required', 'uuid', 'exists:carts,id'],
            'shipping_address' => ['required', 'array'],
            'billing_address' => ['required', 'array'],
            'notes' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'cart_id.required' => 'É necessário informar o carrinho para criar a ordem.',
            'cart_id.uuid' => 'O ID do carrinho informado é inválido.',
            'cart_id.exists' => 'O carrinho informado não existe.',
            'shipping_address.required' => 'O endereço de entrega é obrigatório.',
            'shipping_address.array' => 'O endereço de entrega deve ser enviado em formato de array.',
            'billing_address.required' => 'O endereço de cobrança é obrigatório.',
            'billing_address.array' => 'O endereço de cobrança deve ser enviado em formato de array.',
            'notes.string' => 'As observações devem ser um texto válido.',
        ];
    }
}
