<?php

namespace Database\Factories;

use App\Models\Escala;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class EscalaFactory extends Factory
{
    protected $model = Escala::class;

    public function definition(): array
    {
        return [
            'mes_referencia' => now()->format('Y-m'),
            'status' => 'publicada',
            'created_by' => User::factory(),
        ];
    }
}