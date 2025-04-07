<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Alert;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AlertTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that the model casts attributes correctly.
     */
    public function test_model_casts_attributes_correctly(): void
    {
        $alert = Alert::factory()->create([
            'conditions' => [
                'time_window' => 60,
                'threshold' => 5,
                'sentiment_threshold' => 0.7,
                'keywords' => ['test', 'example'],
            ],
            'notification_channels' => ['email', 'slack'],
            'last_triggered_at' => now(),
        ]);
        
        $this->assertIsArray($alert->conditions);
        $this->assertEquals(60, $alert->conditions['time_window']);
        $this->assertEquals(5, $alert->conditions['threshold']);
        $this->assertEquals(0.7, $alert->conditions['sentiment_threshold']);
        $this->assertEquals(['test', 'example'], $alert->conditions['keywords']);
        
        $this->assertIsArray($alert->notification_channels);
        $this->assertEquals(['email', 'slack'], $alert->notification_channels);
        
        $this->assertInstanceOf(\Carbon\Carbon::class, $alert->last_triggered_at);
    }
    
    /**
     * Test that the model belongs to a user.
     */
    public function test_model_belongs_to_user(): void
    {
        $user = User::factory()->create();
        $alert = Alert::factory()->create([
            'user_id' => $user->id,
        ]);
        
        $this->assertInstanceOf(User::class, $alert->user);
        $this->assertEquals($user->id, $alert->user->id);
    }
} 