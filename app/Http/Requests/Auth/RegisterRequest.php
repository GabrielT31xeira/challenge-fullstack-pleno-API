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
}
