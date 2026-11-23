<?php

namespace Database\Factories;

use App\Models\Alocacao;
use App\Models\Escala;
use App\Models\Posto;
use App\Models\Turno;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AlocacaoFactory extends Factory
{
    protected $model = Alocacao::class;

    public function definition(): array
    {
        return [
            'escala_id' => Escala::factory(),
            'user_id' => User::factory(),
            'posto_id' => Posto::factory(),
            'turno_id' => Turno::factory(),
            'data' => now()->addDays(2)->toDateString(),
            'status' => 'confirmado',
        ];
    }
}