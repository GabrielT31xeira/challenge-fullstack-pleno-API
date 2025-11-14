<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use App\DTO\Auth\LoginDTO;

class LoginRequest extends FormRequest
{
    public function rules()
    {
        return [
            'email'    => 'required|email',
            'password' => 'required|string',
        ];
    }

    public function toDTO(): LoginDTO
    {
        return new LoginDTO(
            $this->email,
            $this->password
        );
    }

    public function messages()
    {
        return [
            'email.required'    => 'O campo e-mail é obrigatório.',
            'email.email'       => 'Informe um endereço de e-mail válido.',

            'password.required' => 'O campo senha é obrigatório.',
            'password.string'   => 'A senha deve ser um texto válido.',
        ];
    }

}
