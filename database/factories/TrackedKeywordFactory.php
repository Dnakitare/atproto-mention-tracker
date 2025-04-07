<?php

namespace Database\Factories;

use App\Models\TrackedKeyword;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TrackedKeywordFactory extends Factory
{
    protected $model = TrackedKeyword::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'keyword' => $this->faker->word(),
            'type' => $this->faker->randomElement(['username', 'hashtag', 'keyword']),
            'is_active' => true,
        ];
    }
} 