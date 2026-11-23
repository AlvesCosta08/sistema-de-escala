<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RejeitarAvisoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'justificativa' => ['nullable', 'string', 'max:1000'],
        ];
    }
}