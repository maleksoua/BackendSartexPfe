<?php

namespace Database\Factories;

use App\Models\Site;
use App\Models\User;
use App\Models\Zone;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory
 */
class GuardFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $chefs = User::where('role', User::ROLE_CHEF)->get();

        return [
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'phone' => $this->faker->unique()->phoneNumber(),
            'register_number' => $this->faker->unique()->creditCardNumber(),
            'tag' => $this->faker->unique()->creditCardNumber(),
            'profile_image' => '202205071556meta-logo-1.png',
            'chef_id' => count($chefs) > 0 ? $chefs->random()->id : null,
        ];
    }

}
