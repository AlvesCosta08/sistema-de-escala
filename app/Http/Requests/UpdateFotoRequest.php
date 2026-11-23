<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFotoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'foto' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'foto.required' => 'Selecione uma imagem.',
            'foto.image' => 'O arquivo enviado não é uma imagem válida.',
            'foto.mimes' => 'Formatos aceitos: JPG, PNG ou WEBP.',
            'foto.max' => 'A imagem não pode ultrapassar 2 MB.',
        ];
    }
}