<?php

namespace Database\Factories;

use App\Models\Alocacao;
use App\Models\AvisoAusencia;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AvisoAusenciaFactory extends Factory
{
    protected $model = AvisoAusencia::class;

    public function definition(): array
    {
        return [
            'alocacao_id' => Alocacao::factory(),
            'user_id' => User::factory(),
            'motivo' => 'imprevisto',
            'observacao' => $this->faker->sentence(),
            'urgente' => false,
            'status' => 'pendente',
        ];
    }

    public function urgente(): static
    {
        return $this->state(fn () => ['urgente' => true]);
    }
}