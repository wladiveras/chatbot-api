<?php

namespace App\Http\Requests;

use App\Enums\UserStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return false;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100', 'min:3'],
            'email' => ['required', 'email', 'max:254'],
            'status' => ['nullable', new Enum(type: UserStatus::class)],
        ];
    }
}
