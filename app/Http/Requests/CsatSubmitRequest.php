<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CsatSubmitRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'rating' => ['required', 'in:good,neutral,poor'],
            'comment' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
