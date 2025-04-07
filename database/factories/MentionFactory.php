<?php

namespace Database\Factories;

use App\Models\Mention;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class MentionFactory extends Factory
{
    protected $model = Mention::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'author_handle' => $this->faker->userName(),
            'text' => $this->faker->sentence(),
            'post_url' => $this->faker->url(),
            'post_indexed_at' => $this->faker->dateTimeThisMonth(),
            'sentiment' => $this->faker->randomFloat(2, -1, 1),
        ];
    }
} 