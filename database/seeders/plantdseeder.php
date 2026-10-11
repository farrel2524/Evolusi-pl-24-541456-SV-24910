<?php

namespace Database\Seeders;

use App\Models\Plantd;
use Illuminate\Database\Seeder;

class PlantdSeeder extends Seeder
{
    public function run(): void
    {
        Plantd::factory()->count(20)->create();
    }
}
