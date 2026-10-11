<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Plantd;

class PlantdFactory extends Factory
{
    protected $model = Plantd::class;

    public function definition()
    {
        return [
            'Pemilik_lahan' => $this->faker->name(),
            'Luas_lahan' => $this->faker->numberBetween(185, 250),
            'created_at' => $this->faker->dateTimeBetween('-5 years', 'now'),
            'updated_at' => now(),
        ];
    }
}
