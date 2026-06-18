<?php

namespace Database\Factories;

use App\Models\Role;
use App\Models\Institution;
use Illuminate\Database\Eloquent\Factories\Factory;

class RoleFactory extends Factory
{
    protected $model = Role::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
            'slug' => fake()->unique()->slug(),
            'description' => fake()->sentence(),
            'is_system' => false,
            'institution_id' => Institution::factory(),
        ];
    }
}
