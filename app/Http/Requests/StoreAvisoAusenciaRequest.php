<?php

namespace App\Http\Requests;

use App\Models\AvisoAusencia;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAvisoAusenciaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('marcarAusencia', $this->route('alocacao'));
    }

    public function rules(): array
    {
        return [
            'motivo' => ['required', Rule::in(AvisoAusencia::MOTIVOS)],
            'observacao' => ['nullable', 'string', 'max:500'],
            'anexo' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ];
    }
}