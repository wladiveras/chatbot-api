<?php

namespace App\Http\Requests;

use App\Enums\MessagesType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MessengerRequest extends FormRequest
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
            'type' => [Rule::enum(MessagesType::class)],
            'message' => ['string'],
            'connection' => ['required', 'string'],
            'number' => ['required', 'string'],
            'delay' => ['integer'],
            'caption' => ['string'],
            'file_url' => ['string'],
        ];
    }
}
