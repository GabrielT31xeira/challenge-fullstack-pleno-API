<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use App\DTO\Auth\RegisterDTO;

class RegisterRequest extends FormRequest
{
    public function rules()
    {
        return [
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:6'
        ];
    }

    public function toDTO(): RegisterDTO
    {
        return new RegisterDTO(
            $this->name,
            $this->email,
            $this->password
        );
    }

    public function messages()
    {
        return [
            'name.required' => 'O campo nome é obrigatório.',
            'name.string'   => 'O nome deve ser um texto válido.',
            'name.max'      => 'O nome não pode ultrapassar 255 caracteres.',

            'email.required' => 'O campo e-mail é obrigatório.',
            'email.email'    => 'Informe um endereço de e-mail válido.',
            'email.unique'   => 'Este e-mail já está cadastrado.',

            'password.required' => 'O campo senha é obrigatório.',
            'password.min'      => 'A senha deve conter pelo menos 6 caracteres.',
        ];
    }

}
