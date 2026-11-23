<?php

namespace Database\Factories;

use App\Models\Turno;
use Illuminate\Database\Eloquent\Factories\Factory;

class TurnoFactory extends Factory
{
    protected $model = Turno::class;

    public function definition(): array
    {
        return [
            'nome' => 'Diurno',
            'hora_inicio' => '07:00:00',
            'hora_fim' => '19:00:00',
            'tolerancia_atraso_min' => 10,
            'intervalo_min' => 60,
        ];
    }

    public function noturno(): static
    {
        return $this->state(fn () => [
            'nome' => 'Noturno',
            'hora_inicio' => '19:00:00',
            'hora_fim' => '07:00:00',
        ]);
    }
}