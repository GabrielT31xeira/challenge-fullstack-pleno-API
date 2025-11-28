<?php

namespace App\Http\Requests\Category;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCategoryRequest extends FormRequest
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
            'name' => 'string|min:2|max:255',
            'slug' => 'string|min:2|max:255|alpha_dash',
            'description' => 'nullable|string|max:500',
            'active' => 'boolean',
            'parent_id' => 'nullable|uuid|exists:categories,id',
        ];
    }

    public function messages(): array
    {
        return [
            // Name
            'name.string' => 'O nome deve ser um texto válido.',
            'name.min' => 'O nome deve ter pelo menos 2 caracteres.',
            'name.max' => 'O nome não pode ter mais de 255 caracteres.',

            // Slug
            'slug.string' => 'O slug deve ser um texto válido.',
            'slug.min' => 'O slug deve ter pelo menos 2 caracteres.',
            'slug.max' => 'O slug não pode ter mais de 255 caracteres.',
            'slug.alpha_dash' => 'O slug só pode conter letras, números, traços e underscores.',

            // Description
            'description.string' => 'A descrição deve ser um texto válido.',
            'description.max' => 'A descrição não pode ter mais de 500 caracteres.',

            // Active
            'active.boolean' => 'O status ativo deve ser verdadeiro ou falso.',

            // Parent ID
            'parent_id.uuid' => 'O ID da categoria pai deve ser um UUID válido.',
            'parent_id.exists' => 'A categoria pai selecionada não existe.',
        ];
    }
}