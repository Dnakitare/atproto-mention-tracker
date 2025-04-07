<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Alert;
use App\Models\Mention;
use App\Services\AlertService;
use App\Services\MentionTrackingService;
use App\Services\SentimentAnalysisService;
use Mockery;

class AlertServiceTest extends TestCase
{
    protected AlertService $alertService;
    protected MentionTrackingService $mentionTrackingService;
    protected SentimentAnalysisService $sentimentAnalysisService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mentionTrackingService = Mockery::mock(MentionTrackingService::class);
        $this->sentimentAnalysisService = Mockery::mock(SentimentAnalysisService::class);
        $this->alertService = new AlertService(
            $this->mentionTrackingService,
            $this->sentimentAnalysisService
        );
    }

    /**
     * Test that the checkSentimentSpike method correctly identifies sentiment spikes.
     */
    public function test_check_sentiment_spike_identifies_spikes(): void
    {
        $user = User::factory()->create();
        $alert = Alert::factory()->create([
            'user_id' => $user->id,
            'type' => 'sentiment_spike',
            'conditions' => [
                'time_window' => 60,
                'sentiment_threshold' => 0.7,
            ],
            'is_active' => true,
        ]);
        
        // Mock sentiment analysis service to return a high sentiment trend
        $this->sentimentAnalysisService
            ->shouldReceive('getSentimentTrend')
            ->once()
            ->with($user->id, 60)
            ->andReturn(0.9);
        
        $result = $this->alertService->checkSentimentSpike($alert);
        
        $this->assertTrue($result);
    }
    
    /**
     * Test that the checkMentionSpike method correctly identifies mention spikes.
     */
    public function test_check_mention_spike_identifies_spikes(): void
    {
        $user = User::factory()->create();
        $alert = Alert::factory()->create([
            'user_id' => $user->id,
            'type' => 'mention_spike',
            'conditions' => [
                'time_window' => 60,
                'threshold' => 5,
            ],
            'is_active' => true,
        ]);
        
        // Mock mention tracking service to return a count above threshold
        $this->mentionTrackingService
            ->shouldReceive('getMentionCount')
            ->once()
            ->with($user->id, 60)
            ->andReturn(6);
        
        $result = $this->alertService->checkMentionSpike($alert);
        
        $this->assertTrue($result);
    }
    
    /**
     * Test that the checkKeywordMatch method correctly identifies keyword matches.
     */
    public function test_check_keyword_match_identifies_matches(): void
    {
        $user = User::factory()->create();
        $alert = Alert::factory()->create([
            'user_id' => $user->id,
            'type' => 'keyword_match',
            'conditions' => [
                'keywords' => ['test', 'important'],
                'time_window' => 60,
            ],
            'is_active' => true,
        ]);
        
        // Create a mention with matching keyword
        $mention = Mention::factory()->create([
            'user_id' => $user->id,
            'text' => 'This is a test mention',
            'post_indexed_at' => now(),
        ]);
        
        // Mock mention tracking service to return our test mention
        $this->mentionTrackingService
            ->shouldReceive('getRecentMentions')
            ->once()
            ->with($alert->user, 60)
            ->andReturn(collect([$mention]));
        
        $result = $this->alertService->checkKeywordMatch($alert);
        
        $this->assertTrue($result);
    }
} 