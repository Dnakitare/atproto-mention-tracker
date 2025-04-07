<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Alert;
use App\Models\Mention;
use App\Notifications\MentionAlert;
use Illuminate\Notifications\Messages\SlackMessage;

class MentionAlertTest extends TestCase
{
    /**
     * Test that the via method returns the correct channels.
     */
    public function test_via_returns_correct_channels(): void
    {
        $user = User::factory()->create();
        $alert = Alert::factory()->create([
            'user_id' => $user->id,
            'name' => 'Test Alert',
            'type' => 'mention_spike',
            'notification_channels' => ['email', 'slack'],
            'is_active' => true,
        ]);
        $mention = Mention::factory()->create([
            'user_id' => $user->id,
            'text' => 'Test mention',
        ]);
        
        $notification = new MentionAlert($alert, collect([$mention]));
        
        // Initially, only database channel should be used
        $this->assertEquals(['database'], $notification->via($user));
        
        // When email notifications are enabled
        $user->notificationSetting()->create([
            'email_notifications' => true,
            'in_app_notifications' => true,
            'notification_preferences' => [
                'mention_spike' => true,
                'sentiment_spike' => true,
                'keyword_match' => true,
            ],
        ]);
        
        // Refresh user to get updated settings
        $user->refresh();
        
        // Database and mail channels should be used
        $channels = $notification->via($user);
        $this->assertContains('database', $channels);
        $this->assertContains('mail', $channels);
        
        // When Slack webhook URL is added
        $user->notificationSetting()->update([
            'slack_webhook_url' => 'https://hooks.slack.com/services/xxx/yyy/zzz',
        ]);
        
        // Refresh user to get updated settings
        $user->refresh();
        
        // Database, mail, and Slack channels should be used
        $channels = $notification->via($user);
        $this->assertContains('database', $channels);
        $this->assertContains('mail', $channels);
        $this->assertContains('slack', $channels);
    }
    
    /**
     * Test that the toMail method returns the correct mail message.
     */
    public function test_to_mail_returns_correct_message(): void
    {
        $user = User::factory()->create();
        $alert = Alert::factory()->create([
            'user_id' => $user->id,
            'name' => 'Test Alert',
            'type' => 'mention_spike',
            'is_active' => true,
        ]);
        $mention = Mention::factory()->create([
            'user_id' => $user->id,
            'text' => 'Test mention',
        ]);
        
        $notification = new MentionAlert($alert, collect([$mention]));
        $mailMessage = $notification->toMail($user);
        
        $this->assertEquals('New Mention Alert', $mailMessage->subject);
        $this->assertStringContainsString('You have a new mention alert.', $mailMessage->introLines[0]);
        $this->assertStringContainsString('Alert Type: mention_spike', $mailMessage->introLines[1]);
        $this->assertStringContainsString('Number of Mentions: 1', $mailMessage->introLines[2]);
    }
    
    /**
     * Test that the toSlack method returns the correct slack message.
     */
    public function test_to_slack_returns_correct_message(): void
    {
        $user = User::factory()->create();
        $alert = Alert::factory()->create([
            'user_id' => $user->id,
            'name' => 'Test Alert',
            'type' => 'mention_spike',
            'description' => 'Test description',
            'is_active' => true,
        ]);
        $mention = Mention::factory()->create([
            'user_id' => $user->id,
            'text' => 'Test mention',
        ]);
        
        $notification = new MentionAlert($alert, collect([$mention]));
        $slackMessage = $notification->toSlack($user);
        
        $this->assertInstanceOf(SlackMessage::class, $slackMessage);
        $this->assertEquals("New Alert: Test Alert", $slackMessage->content);
        $this->assertEquals('Alert Details', $slackMessage->attachments[0]->title);
        $this->assertEquals([
            'Alert Type' => 'mention_spike',
            'Number of Mentions' => '1 mention',
            'Description' => 'Test description',
        ], $slackMessage->attachments[0]->fields);
    }
    
    /**
     * Test that the toArray method returns the correct array.
     */
    public function test_to_array_returns_correct_array(): void
    {
        $user = User::factory()->create();
        $alert = Alert::factory()->create([
            'user_id' => $user->id,
            'name' => 'Test Alert',
            'type' => 'mention_spike',
            'is_active' => true,
        ]);
        $mention = Mention::factory()->create([
            'user_id' => $user->id,
            'text' => 'Test mention',
        ]);
        
        $notification = new MentionAlert($alert, collect([$mention]));
        $array = $notification->toArray($user);
        
        $this->assertEquals($alert->id, $array['alert_id']);
        $this->assertEquals('mention_spike', $array['alert_type']);
        $this->assertEquals(1, $array['mention_count']);
    }
} 