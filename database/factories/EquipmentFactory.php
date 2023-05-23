<?php

namespace Database\Factories;

use App\Models\Site;
use App\Models\User;
use App\Models\Zone;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory
 */
class EquipmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $zones = Zone::get();

        return [
            'name' => $this->faker->firstName(),
            'zone_id' => count($zones) > 0 ? $zones->random()->id : null,
        ];
    }

}
