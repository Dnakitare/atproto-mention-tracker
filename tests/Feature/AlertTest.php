<?php

namespace Tests\Feature;

use App\Models\Alert;
use App\Models\Mention;
use App\Models\User;
use App\Jobs\ProcessAlertNotifications;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;
use App\Services\AlertService;
use Illuminate\Support\Facades\Notification;
use App\Notifications\MentionAlert;
use App\Services\MentionTrackingService;
use App\Services\SentimentAnalysisService;

class AlertTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_alert()
    {
        $user = User::factory()->create();
        
        $response = $this->actingAs($user)->post(route('alerts.store'), [
            'name' => 'Test Alert',
            'description' => 'This is a test alert',
            'conditions' => ['sentiment_positive', 'has_media'],
            'notification_frequency' => 'immediate',
            'notification_channels' => ['email'],
        ]);
        
        $response->assertRedirect(route('alerts.index'));
        $this->assertDatabaseHas('alerts', [
            'user_id' => $user->id,
            'name' => 'Test Alert',
            'description' => 'This is a test alert',
            'notification_frequency' => 'immediate',
            'type' => 'sentiment_spike',
        ]);
    }

    public function test_alert_notification_is_queued_when_conditions_met()
    {
        Queue::fake();
        
        $user = User::factory()->create();
        $alert = Alert::factory()->create([
            'user_id' => $user->id,
            'conditions' => ['sentiment_positive'],
            'is_active' => true,
        ]);
        
        $mention = Mention::factory()->create([
            'sentiment' => 'positive',
        ]);
        
        ProcessAlertNotifications::dispatchJob($alert, $mention);
        
        Queue::assertPushed(ProcessAlertNotifications::class, function ($job) use ($alert, $mention) {
            return $job->alert->id === $alert->id && $job->mention->id === $mention->id;
        });
    }

    public function test_alert_is_not_processed_when_inactive()
    {
        Queue::fake();
        
        $user = User::factory()->create();
        $alert = Alert::factory()->create([
            'user_id' => $user->id,
            'conditions' => ['sentiment_positive'],
            'is_active' => false,
        ]);
        
        $mention = Mention::factory()->create([
            'sentiment' => 'positive',
        ]);
        
        ProcessAlertNotifications::dispatchJob($alert, $mention);
        
        Queue::assertNothingPushed();
    }

    public function test_mention_spike_alert_is_triggered()
    {
        Queue::fake();
        
        $user = User::factory()->create();
        $alert = Alert::factory()->create([
            'user_id' => $user->id,
            'type' => 'mention_spike',
            'conditions' => [
                'threshold' => 1,
                'time_window' => 60
            ],
            'is_active' => true,
        ]);
        
        $mention = Mention::factory()->create([
            'user_id' => $user->id,
            'post_indexed_at' => now(),
        ]);
        
        ProcessAlertNotifications::dispatchJob($alert, $mention);
        
        Queue::assertPushed(ProcessAlertNotifications::class);
    }

    public function test_sentiment_spike_alert_is_triggered()
    {
        Queue::fake();
        
        $user = User::factory()->create();
        $alert = Alert::factory()->create([
            'user_id' => $user->id,
            'type' => 'sentiment_spike',
            'conditions' => [
                'sentiment' => 'negative',
                'threshold' => 1
            ],
            'is_active' => true,
        ]);
        
        $mention = Mention::factory()->create([
            'user_id' => $user->id,
            'post_indexed_at' => now(),
            'sentiment' => 'negative',
        ]);
        
        ProcessAlertNotifications::dispatchJob($alert, $mention);
        
        Queue::assertPushed(ProcessAlertNotifications::class);
    }

    public function test_keyword_match_alert_is_triggered()
    {
        Queue::fake();
        
        $user = User::factory()->create();
        $alert = Alert::factory()->create([
            'user_id' => $user->id,
            'type' => 'keyword_match',
            'conditions' => [
                'keywords' => ['test', 'important'],
                'threshold' => 1
            ],
            'is_active' => true,
        ]);
        
        $mention = Mention::factory()->create([
            'user_id' => $user->id,
            'post_indexed_at' => now(),
            'text' => 'This is a test mention',
        ]);
        
        ProcessAlertNotifications::dispatchJob($alert, $mention);
        
        Queue::assertPushed(ProcessAlertNotifications::class);
    }

    public function test_notification_frequency_limits()
    {
        Queue::fake();
        
        $user = User::factory()->create();
        $alert = Alert::factory()->create([
            'user_id' => $user->id,
            'type' => 'keyword_match',
            'conditions' => [
                'keywords' => ['test'],
                'threshold' => 1
            ],
            'is_active' => true,
            'notification_frequency' => 'hourly',
            'last_triggered_at' => now()->subMinutes(30),
        ]);
        
        $mention = Mention::factory()->create([
            'user_id' => $user->id,
            'post_indexed_at' => now(),
            'text' => 'This is a test mention',
        ]);
        
        // Create the job
        $job = new ProcessAlertNotifications($alert, $mention);
        
        // Use reflection to access protected method
        $method = new \ReflectionMethod($job, 'shouldProcessAlert');
        $method->setAccessible(true);
        
        // Should not trigger because last trigger was less than an hour ago
        $this->assertFalse($method->invoke($job));
        
        // Update last_triggered_at to be more than an hour ago
        $alert->update(['last_triggered_at' => now()->subHours(2)]);
        
        // Should trigger because last trigger was more than an hour ago
        $this->assertTrue($method->invoke($job));
    }

    /**
     * Test that a slack notification is sent when configured.
     */
    public function test_slack_notification_is_sent_when_configured(): void
    {
        // Create a user with notification settings
        $user = User::factory()->create();
        $user->notificationSetting()->create([
            'email_notifications' => true,
            'in_app_notifications' => true,
            'slack_webhook_url' => 'https://hooks.slack.com/services/xxx/yyy/zzz',
            'notification_preferences' => [
                'mention_spike' => true,
                'sentiment_spike' => true,
                'keyword_match' => true,
            ],
        ]);
        
        // Create an alert
        $alert = Alert::factory()->create([
            'user_id' => $user->id,
            'name' => 'Test Alert',
            'description' => 'Test Description',
            'type' => 'sentiment_spike',
            'conditions' => [
                'time_window' => 60,
                'sentiment_threshold' => 0.7,
            ],
            'notification_channels' => ['slack'],
            'is_active' => true,
        ]);
        
        // Create a mention with positive sentiment
        $mention = Mention::factory()->create([
            'user_id' => $user->id,
            'sentiment' => 0.9,
            'text' => 'Test mention with positive sentiment',
        ]);
        
        // Mock the sentiment analysis service
        $sentimentAnalysisService = $this->mock(SentimentAnalysisService::class);
        $sentimentAnalysisService->shouldReceive('getSentimentTrend')
            ->once()
            ->with($user->id, 60)
            ->andReturn(0.9);
        
        // Process the alert
        $alertService = new AlertService(
            app(MentionTrackingService::class),
            $sentimentAnalysisService
        );
        
        // Fake notifications
        Notification::fake();
        
        $alertService->processAlert($alert, $mention);
        
        // Assert that the notification was sent
        Notification::assertSentTo(
            $user,
            MentionAlert::class,
            function ($notification) use ($alert, $mention, $user) {
                $array = $notification->toArray($user);
                return $array['alert_id'] === $alert->id;
            }
        );
    }
} 