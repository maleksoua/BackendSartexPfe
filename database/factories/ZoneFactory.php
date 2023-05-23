<?php

namespace Database\Factories;

use App\Models\Site;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory
 */
class ZoneFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $sites = Site::get();
        $chefs = User::where('role', User::ROLE_CHEF)->get();

        return [
            'name' => $this->faker->unique()->company(),
            'site_id' => count($sites) > 0 ? $sites->random()->id : null,
            'chef_id' => count($chefs) > 0 ? $chefs->random()->id : null,
            'image' => '202205071556meta-logo-1.png',
        ];
    }

}
