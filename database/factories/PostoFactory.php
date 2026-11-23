<?php

namespace Database\Factories;

use App\Models\Posto;
use Illuminate\Database\Eloquent\Factories\Factory;

class PostoFactory extends Factory
{
    protected $model = Posto::class;

    public function definition(): array
    {
        return [
            'nome' => 'Portaria '.$this->faker->unique()->citySuffix(),
            'descricao' => $this->faker->sentence(4),
            'localizacao' => $this->faker->address(),
            'ativo' => true,
        ];
    }
}