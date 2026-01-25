<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $userId = $this->route('user') ? $this->route('user')->id : null;

        return [
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'email' => 'required|email|unique:users,email,' . $userId,
            'phone' => 'nullable|string|max:20',
            'password' => $this->isMethod('post') ? 'required|min:6' : 'nullable|min:6',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'is_active' => 'boolean',
            'role_id' => 'nullable|exists:roles,id',
            'bank_name' => 'nullable|string|max:255',
            'iban' => 'nullable|string|max:50',
            'account_number' => 'nullable|string|max:50',
            'swift' => 'nullable|string|max:50',
            'experience' => 'nullable|string',
            'certificates' => 'nullable|string',
            'notes' => 'nullable|string',
            'balance' => 'nullable|numeric',

        ];
    }
}
