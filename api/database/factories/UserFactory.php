<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    public function definition()
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => Hash::make('12345678'),
            'telephone' => '+229' . fake()->randomNumber(8, true),
            'dateOfBirth' => '2000-01-01',
            'is_active' => true,
            'remember_token' => Str::random(10),
        ];
    }
}
