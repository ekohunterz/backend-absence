<?php

namespace Database\Factories;

use App\Models\Grade;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Student>
 */
class StudentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {

        return [
            'nis' => $this->faker->unique()->numerify('#####'),
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'gender' => $this->faker->randomElement(['L', 'P']),
            'birth_date' => $this->faker->date('Y-m-d', '2010-12-31'),
            'address' => $this->faker->address(),
            'phone' => $this->faker->phoneNumber(),
            'password' => Hash::make('password'), // default password
            'grade_id' => Grade::all()->random()->id,
            'parent_name' => $this->faker->name(),
            'remember_token' => null,
        ];
    }
}
