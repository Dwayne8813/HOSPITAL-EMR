<?php

namespace Database\Factories;

use App\Models\Patient;
use App\Models\Institution;
use Illuminate\Database\Eloquent\Factories\Factory;

class PatientFactory extends Factory
{
    protected $model = Patient::class;

    public function definition(): array
    {
        return [
            'institution_id' => Institution::factory(),
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'birth_date' => fake()->dateTimeBetween('-80 years', '-18 years')->format('Y-m-d'),
            'age' => fake()->numberBetween(18, 80),
            'gender' => fake()->randomElement(['M', 'F', 'O']),
            'civil_status' => fake()->randomElement(['single', 'married', 'divorced', 'widowed']),
            'nationality' => 'Boliviana',
            'phone' => fake()->phoneNumber(),
            'email' => fake()->safeEmail(),
            'blood_type' => fake()->randomElement(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-']),
            'emergency_contact_name' => fake()->name(),
            'emergency_contact_phone' => fake()->phoneNumber(),
            'status' => 'active',
            'ci' => fake()->numerify('########'),
        ];
    }
}
