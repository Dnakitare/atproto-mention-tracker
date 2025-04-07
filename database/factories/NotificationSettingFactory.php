<?php

namespace Database\Factories;

use App\Models\NotificationSetting;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class NotificationSettingFactory extends Factory
{
    protected $model = NotificationSetting::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'email_notifications' => $this->faker->boolean(80),
            'in_app_notifications' => $this->faker->boolean(80),
            'email_frequency' => $this->faker->randomElement(['immediate', 'hourly', 'daily', 'weekly']),
            'notification_preferences' => [
                'mention_spike' => true,
                'sentiment_spike' => true,
                'keyword_match' => true,
            ],
            'slack_webhook_url' => $this->faker->optional(0.5)->url(),
        ];
    }
} 