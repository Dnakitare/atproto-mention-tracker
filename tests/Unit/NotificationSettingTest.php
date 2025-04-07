<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\NotificationSetting;
use App\Models\User;

class NotificationSettingTest extends TestCase
{
    /**
     * Test that the model casts attributes correctly.
     */
    public function test_model_casts_attributes_correctly(): void
    {
        $notificationSetting = NotificationSetting::factory()->create([
            'email_notifications' => true,
            'in_app_notifications' => true,
            'notification_preferences' => [
                'mention_spike' => true,
                'sentiment_spike' => true,
                'keyword_match' => true,
            ],
        ]);
        
        $this->assertIsBool($notificationSetting->email_notifications);
        $this->assertIsBool($notificationSetting->in_app_notifications);
        $this->assertIsArray($notificationSetting->notification_preferences);
        $this->assertTrue($notificationSetting->notification_preferences['mention_spike']);
        $this->assertTrue($notificationSetting->notification_preferences['sentiment_spike']);
        $this->assertTrue($notificationSetting->notification_preferences['keyword_match']);
    }
    
    /**
     * Test that the model belongs to a user.
     */
    public function test_model_belongs_to_user(): void
    {
        $user = User::factory()->create();
        $notificationSetting = NotificationSetting::factory()->create([
            'user_id' => $user->id,
        ]);
        
        $this->assertInstanceOf(User::class, $notificationSetting->user);
        $this->assertEquals($user->id, $notificationSetting->user->id);
    }
} 