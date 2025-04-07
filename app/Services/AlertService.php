<?php

namespace App\Services;

use App\Models\User;
use App\Models\Mention;
use App\Models\Alert;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Notification;
use App\Notifications\MentionAlert;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Service class for handling alert-related operations.
 * 
 * This service is responsible for creating, processing, and triggering alerts
 * based on various conditions such as mention spikes, sentiment spikes, and keyword matches.
 */
class AlertService
{
    private MentionTrackingService $mentionTrackingService;
    private SentimentAnalysisService $sentimentAnalysisService;

    /**
     * Create a new AlertService instance.
     *
     * @param MentionTrackingService $mentionTrackingService Service for tracking mentions
     * @param SentimentAnalysisService $sentimentAnalysisService Service for analyzing sentiment
     */
    public function __construct(
        MentionTrackingService $mentionTrackingService,
        SentimentAnalysisService $sentimentAnalysisService
    ) {
        $this->mentionTrackingService = $mentionTrackingService;
        $this->sentimentAnalysisService = $sentimentAnalysisService;
    }

    /**
     * Create a new alert for a user.
     *
     * @param User $user The user to create the alert for
     * @param array $data Alert data including name, type, conditions, etc.
     * @return Alert The created alert
     * @throws \InvalidArgumentException If required data is missing
     */
    public function createAlert(User $user, array $data): Alert
    {
        // Validate required fields
        if (empty($data['name']) || empty($data['type']) || empty($data['conditions'])) {
            throw new \InvalidArgumentException('Alert name, type, and conditions are required');
        }

        return Alert::create([
            'user_id' => $user->id,
            'name' => $data['name'],
            'type' => $data['type'],
            'conditions' => $data['conditions'],
            'is_active' => $data['is_active'] ?? true,
            'notification_channels' => $data['notification_channels'] ?? ['email'],
        ]);
    }

    /**
     * Check for alert conditions for a specific user.
     *
     * @param User $user The user to check alerts for
     * @return void
     */
    public function checkAlerts(User $user): void
    {
        try {
            $alerts = Alert::where('user_id', $user->id)
                ->where('is_active', true)
                ->get();

            foreach ($alerts as $alert) {
                if ($this->shouldTriggerAlert($alert)) {
                    $this->triggerAlert($alert);
                }
            }
        } catch (\Exception $e) {
            Log::error('Error checking alerts for user: ' . $user->id, [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Determine if an alert should be triggered based on its type and conditions.
     *
     * @param Alert $alert The alert to check
     * @return bool Whether the alert should be triggered
     */
    private function shouldTriggerAlert(Alert $alert): bool
    {
        try {
            $mentions = $this->getRelevantMentions($alert);
            
            switch ($alert->type) {
                case 'mention_spike':
                    return $this->checkMentionSpike($alert);
                
                case 'sentiment_spike':
                    return $this->checkSentimentSpike($alert);
                
                case 'keyword_match':
                    return $this->checkKeywordMatch($alert);
                
                default:
                    Log::warning('Unknown alert type: ' . $alert->type);
                    return false;
            }
        } catch (\Exception $e) {
            Log::error('Error determining if alert should trigger: ' . $alert->id, [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Get relevant mentions for the alert based on time window and conditions.
     *
     * @param Alert $alert The alert to get mentions for
     * @return Collection Collection of relevant mentions
     */
    private function getRelevantMentions(Alert $alert): Collection
    {
        // Use cache to avoid repeated database queries
        $cacheKey = "alert_mentions_{$alert->id}_{$alert->user_id}";
        
        return Cache::remember($cacheKey, 60, function () use ($alert) {
            $query = Mention::where('user_id', $alert->user_id);
            
            // Add time window filter
            $timeWindow = $alert->conditions['time_window'] ?? 60;
            $query->whereBetween('created_at', [
                now()->subMinutes($timeWindow),
                now()
            ]);
            
            // Add keyword filter if specified
            if (!empty($alert->conditions['keywords'])) {
                $query->where(function ($q) use ($alert) {
                    foreach ($alert->conditions['keywords'] as $keyword) {
                        $q->orWhere('text', 'like', "%{$keyword}%");
                    }
                });
            }
            
            return $query->get();
        });
    }

    /**
     * Check if there's a mention spike based on the given conditions.
     *
     * @param Alert $alert The alert to check
     * @return bool Whether there's a mention spike
     */
    public function checkMentionSpike(Alert $alert): bool
    {
        if (!$alert->is_active) {
            return false;
        }

        $timeWindow = $alert->conditions['time_window'] ?? 60; // Default to 60 minutes
        $threshold = $alert->conditions['threshold'] ?? 5; // Default to 5 mentions
        
        // Get mention count from service
        $mentionCount = $this->mentionTrackingService->getMentionCount($alert->user_id, $timeWindow);
        
        // Check if mention count exceeds threshold
        return $mentionCount >= $threshold;
    }

    /**
     * Check if there's a sentiment spike based on the alert conditions.
     *
     * @param Alert $alert The alert to check
     * @return bool Whether there's a sentiment spike
     */
    public function checkSentimentSpike(Alert $alert): bool
    {
        if (!$alert->is_active) {
            return false;
        }

        $timeWindow = $alert->conditions['time_window'] ?? 60; // Default to 60 minutes
        $threshold = $alert->conditions['sentiment_threshold'] ?? 0.7; // Default to 0.7
        
        // Get sentiment trend from service
        $sentimentTrend = $this->sentimentAnalysisService->getSentimentTrend($alert->user_id, $timeWindow);
        
        // Check if sentiment exceeds threshold
        return $sentimentTrend >= $threshold;
    }

    /**
     * Check if there's a keyword match in the mentions.
     *
     * @param Alert $alert The alert to check
     * @return bool Whether there's a keyword match
     */
    public function checkKeywordMatch(Alert $alert): bool
    {
        if (!$alert->is_active) {
            return false;
        }

        $timeWindow = $alert->conditions['time_window'] ?? 60; // Default to 60 minutes
        $keywords = $alert->conditions['keywords'] ?? [];
        
        if (empty($keywords)) {
            return false;
        }
        
        // Get recent mentions from service
        $recentMentions = $this->mentionTrackingService->getRecentMentions($alert->user, $timeWindow);
        
        // Check if any mention contains any of the keywords
        foreach ($recentMentions as $mention) {
            foreach ($keywords as $keyword) {
                if (stripos($mention->text, $keyword) !== false) {
                    return true;
                }
            }
        }
        
        return false;
    }

    /**
     * Trigger the alert by sending notifications.
     *
     * @param Alert $alert The alert to trigger
     * @return void
     */
    private function triggerAlert(Alert $alert): void
    {
        try {
            $user = $alert->user;
            $mentions = $this->getRelevantMentions($alert);
            
            // Send notification to all configured channels
            Notification::send($user, new MentionAlert($alert, $mentions));
            
            // Update last triggered timestamp
            $alert->update(['last_triggered_at' => now()]);
            
            // Clear the cache for this alert's mentions
            Cache::forget("alert_mentions_{$alert->id}_{$alert->user_id}");
        } catch (\Exception $e) {
            Log::error('Error triggering alert: ' . $alert->id, [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Process an alert for a specific mention.
     *
     * @param Alert $alert The alert to process
     * @param Mention $mention The mention to process
     * @return void
     */
    public function processAlert(Alert $alert, Mention $mention): void
    {
        try {
            $shouldTrigger = match ($alert->type) {
                'mention_spike' => $this->checkMentionSpike($alert),
                'sentiment_spike' => $this->checkSentimentSpike($alert),
                'keyword_match' => $this->checkKeywordMatch($alert),
                default => false,
            };
            
            if ($shouldTrigger) {
                $this->triggerAlert($alert);
            }
        } catch (\Exception $e) {
            Log::error('Error processing alert: ' . $alert->id, [
                'mention_id' => $mention->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
} 