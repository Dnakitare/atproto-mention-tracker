<?php

namespace Database\Factories;

use App\Models\Alert;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AlertFactory extends Factory
{
    protected $model = Alert::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => $this->faker->sentence(),
            'type' => $this->faker->randomElement(['mention_spike', 'sentiment_spike', 'keyword_match']),
            'conditions' => [
                'time_window' => 60,
                'threshold' => 5,
                'sentiment_threshold' => 0.7,
                'keywords' => ['test', 'example'],
            ],
            'notification_channels' => ['email', 'slack'],
            'last_triggered_at' => null,
            'is_active' => true,
            'notification_frequency' => $this->faker->randomElement(['immediate', 'hourly', 'daily', 'weekly']),
        ];
    }
} 