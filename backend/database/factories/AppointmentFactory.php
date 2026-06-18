<?php

namespace Database\Factories;

use App\Models\Appointment;
use App\Models\Institution;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AppointmentFactory extends Factory
{
    protected $model = Appointment::class;

    public function definition(): array
    {
        $date = fake()->dateTimeBetween('-30 days', '+30 days');

        return [
            'institution_id' => Institution::factory(),
            'patient_id' => Patient::factory(),
            'doctor_id' => User::factory(),
            'appointment_date' => $date->format('Y-m-d'),
            'start_time' => fake()->time('H:i:s'),
            'end_time' => fake()->time('H:i:s'),
            'status' => fake()->randomElement(['scheduled', 'confirmed', 'completed', 'cancelled', 'no_show']),
            'reason' => fake()->sentence(),
            'notes' => fake()->optional()->sentence(),
        ];
    }
}
