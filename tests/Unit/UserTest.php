<?php

namespace Tests\Unit;

use App\Models\Alert;
use App\Models\Mention;
use App\Models\NotificationSetting;
use App\Models\TrackedKeyword;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_has_many_mentions()
    {
        $user = User::factory()->create();
        $mention = Mention::factory()->create(['user_id' => $user->id]);

        $this->assertTrue($user->mentions->contains($mention));
        $this->assertInstanceOf(Mention::class, $user->mentions->first());
    }

    public function test_user_has_many_tracked_keywords()
    {
        $user = User::factory()->create();
        $trackedKeyword = TrackedKeyword::factory()->create(['user_id' => $user->id]);

        $this->assertTrue($user->trackedKeywords->contains($trackedKeyword));
        $this->assertInstanceOf(TrackedKeyword::class, $user->trackedKeywords->first());
    }

    public function test_user_has_one_notification_setting()
    {
        $user = User::factory()->create();
        $notificationSetting = NotificationSetting::factory()->create(['user_id' => $user->id]);

        $this->assertEquals($notificationSetting->id, $user->notificationSetting->id);
        $this->assertInstanceOf(NotificationSetting::class, $user->notificationSetting);
    }

    public function test_user_has_many_alerts()
    {
        $user = User::factory()->create();
        $alert = Alert::factory()->create(['user_id' => $user->id]);

        $this->assertTrue($user->alerts->contains($alert));
        $this->assertInstanceOf(Alert::class, $user->alerts->first());
    }

    public function test_route_notification_for_slack()
    {
        // Test with no notification settings
        $user = User::factory()->create();
        $this->assertEquals('', $user->routeNotificationForSlack());

        // Test with notification settings and slack webhook URL
        $webhookUrl = 'https://hooks.slack.com/services/xxx/yyy/zzz';
        $notificationSetting = NotificationSetting::factory()->create([
            'user_id' => $user->id,
            'slack_webhook_url' => $webhookUrl,
        ]);

        // Refresh the user model to load the new notification settings
        $user->refresh();
        
        $this->assertEquals($webhookUrl, $user->routeNotificationForSlack());
    }

    public function test_fillable_attributes()
    {
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $this->assertEquals('Test User', $user->name);
        $this->assertEquals('test@example.com', $user->email);
    }

    public function test_hidden_attributes()
    {
        $user = User::factory()->create();
        $array = $user->toArray();

        $this->assertArrayNotHasKey('password', $array);
        $this->assertArrayNotHasKey('remember_token', $array);
    }

    public function test_casts_attributes()
    {
        $user = User::factory()->create();

        $this->assertIsString($user->password);
        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $user->email_verified_at);
    }
} 