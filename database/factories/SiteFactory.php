<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory
 */
class SiteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $superChefs = User::where('role', User::ROLE_SUPER_CHEF)->get();
        return [
            'name' => $this->faker->unique()->company(),
            'super_chef_id' => count($superChefs) > 0 ? $superChefs->random()->id : null,
        ];
    }

}
