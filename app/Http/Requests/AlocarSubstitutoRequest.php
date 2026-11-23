<?php

namespace App\Http\Requests;

use App\Models\AvisoAusencia;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AlocarSubstitutoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isAdmin();
    }

    public function rules(): array
    {
        /** @var AvisoAusencia $aviso */
        $aviso = $this->route('aviso');

        // Fonte única de verdade: os mesmos elegíveis que a UI mostra.
        $elegiveis = User::elegiveisParaSubstituir($aviso->alocacao)
            ->pluck('id')
            ->all();

        return [
            'substituto_user_id' => [
                'required',
                'integer',
                Rule::in($elegiveis),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'substituto_user_id.in' => 'Este colaborador não é elegível para substituir (cargo diferente, conflito de turno ou intervalo de descanso insuficiente).',
        ];
    }
}