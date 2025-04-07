<?php

namespace App\Services;

use App\Models\Mention;
use App\Models\TrackedKeyword;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class MentionTrackingService
{
    private BlueskyService $blueskyService;
    private NotificationService $notificationService;
    private SentimentAnalysisService $sentimentAnalysisService;

    public function __construct(BlueskyService $blueskyService, NotificationService $notificationService, SentimentAnalysisService $sentimentAnalysisService)
    {
        $this->blueskyService = $blueskyService;
        $this->notificationService = $notificationService;
        $this->sentimentAnalysisService = $sentimentAnalysisService;
    }

    /**
     * Track mentions for a user
     */
    public function trackMentions(User $user): void
    {
        // Get the user's tracked keywords
        $keywords = $user->trackedKeywords()
            ->where('is_active', true)
            ->pluck('keyword')
            ->toArray();
        
        // Get mentions from Bluesky
        $mentions = $this->blueskyService->getMentions($user, $keywords);
        
        // Process each mention
        foreach ($mentions as $mentionData) {
            // Create the mention
            $mention = Mention::create([
                'user_id' => $user->id,
                'author_handle' => $mentionData['author_handle'],
                'text' => $mentionData['text'],
                'post_url' => $mentionData['post_url'],
                'post_indexed_at' => $mentionData['post_indexed_at'],
            ]);
            
            // Analyze sentiment
            $sentiment = $this->sentimentAnalysisService->analyzeSentiment($mention->text);
            $mention->update(['sentiment' => $sentiment]);
            
            // Send notification
            $this->notificationService->sendNewMentionNotification($user, $mention);
            
            // Process alerts
            $this->processAlerts($user, $mention);
        }
    }
    
    /**
     * Process alerts for a mention
     */
    private function processAlerts(User $user, Mention $mention): void
    {
        // Get the user's active alerts
        $alerts = $user->alerts()
            ->where('type', '!=', 'keyword_match')
            ->get();
        
        // Process each alert
        foreach ($alerts as $alert) {
            // Dispatch job to process alert
            ProcessAlertNotifications::dispatch($alert, $mention);
        }
    }

    /**
     * Track mentions for a specific user
     */
    public function trackMentionsForUser(User $user): void
    {
        $keywords = $user->trackedKeywords()
            ->where('is_active', true)
            ->get();

        foreach ($keywords as $keyword) {
            $this->trackKeyword($user, $keyword);
        }
    }

    /**
     * Track mentions for a specific keyword
     */
    private function trackKeyword(User $user, TrackedKeyword $keyword): void
    {
        $searchQuery = $this->buildSearchQuery($keyword);
        $results = $this->blueskyService->searchPosts($searchQuery);

        if (empty($results['posts'])) {
            return;
        }

        foreach ($results['posts'] as $post) {
            $this->processMention($user, $post);
        }
    }

    /**
     * Build search query based on keyword type
     */
    private function buildSearchQuery(TrackedKeyword $keyword): string
    {
        return match ($keyword->type) {
            'username' => '@' . $keyword->keyword,
            'hashtag' => '#' . $keyword->keyword,
            default => $keyword->keyword,
        };
    }

    /**
     * Process and store a mention
     */
    private function processMention(User $user, array $post): void
    {
        // Check if mention already exists
        if (Mention::where('post_id', $post['uri'])->exists()) {
            return;
        }

        // Create new mention
        $mention = Mention::create([
            'user_id' => $user->id,
            'post_id' => $post['uri'],
            'author_did' => $post['author']['did'],
            'author_handle' => $post['author']['handle'],
            'post_text' => $post['record']['text'],
            'post_data' => $post,
            'post_indexed_at' => Carbon::parse($post['indexedAt']),
        ]);

        // Send notification
        $this->notificationService->sendMentionNotification($user, $mention);
    }

    /**
     * Get recent mentions for a user within a time window
     */
    public function getRecentMentions(User $user, int $timeWindow = 60): Collection
    {
        return $user->mentions()
            ->where('post_indexed_at', '>=', now()->subMinutes($timeWindow))
            ->orderBy('post_indexed_at', 'desc')
            ->get();
    }

    /**
     * Get mentions by keyword
     */
    public function getMentionsByKeyword(User $user, string $keyword): Collection
    {
        return $user->mentions()
            ->where('post_text', 'like', "%{$keyword}%")
            ->orderBy('post_indexed_at', 'desc')
            ->get();
    }

    /**
     * Search mentions with advanced filters
     */
    public function searchMentions(User $user, array $filters = []): Collection
    {
        $query = $user->mentions()
            ->with('keyword')
            ->orderBy('post_indexed_at', 'desc');

        // Date range filter
        if (!empty($filters['start_date'])) {
            $query->where('post_indexed_at', '>=', Carbon::parse($filters['start_date']));
        }
        if (!empty($filters['end_date'])) {
            $query->where('post_indexed_at', '<=', Carbon::parse($filters['end_date']));
        }

        // Author filter
        if (!empty($filters['author'])) {
            $query->where('author_handle', 'like', '%' . $filters['author'] . '%');
        }

        // Keyword filter
        if (!empty($filters['keyword_id'])) {
            $query->where('keyword_id', $filters['keyword_id']);
        }

        // Text search
        if (!empty($filters['search'])) {
            $searchTerm = $filters['search'];
            $query->where(function ($q) use ($searchTerm) {
                $q->where('post_text', 'like', "%{$searchTerm}%")
                  ->orWhere('author_handle', 'like', "%{$searchTerm}%");
            });
        }

        // Regular expression search
        if (!empty($filters['regex'])) {
            try {
                $query->whereRaw('post_text REGEXP ?', [$filters['regex']]);
            } catch (\Exception $e) {
                report($e);
            }
        }

        return $query->get();
    }

    /**
     * Get mention trends
     */
    public function getMentionTrends(User $user, int $days = 30): array
    {
        $startDate = now()->subDays($days);
        
        $mentions = $user->mentions()
            ->where('post_indexed_at', '>=', $startDate)
            ->get();

        return [
            'total_mentions' => $mentions->count(),
            'unique_authors' => $mentions->unique('author_handle')->count(),
            'trending_keywords' => $this->getTrendingKeywords($mentions),
            'peak_hours' => $this->getPeakHours($mentions),
        ];
    }

    /**
     * Get trending keywords from mentions
     */
    private function getTrendingKeywords(Collection $mentions): array
    {
        return $mentions
            ->groupBy(function ($mention) {
                return $mention->keyword->keyword ?? 'Unknown';
            })
            ->map(function ($group) {
                return [
                    'count' => $group->count(),
                    'recent' => $group->sortByDesc('post_indexed_at')->first()->post_indexed_at,
                ];
            })
            ->sortByDesc(function ($stats) {
                return $stats['count'];
            })
            ->take(5)
            ->toArray();
    }

    /**
     * Get peak hours for mentions
     */
    private function getPeakHours(Collection $mentions): array
    {
        return $mentions
            ->groupBy(function ($mention) {
                return $mention->post_indexed_at->format('H');
            })
            ->map->count()
            ->sortDesc()
            ->take(5)
            ->toArray();
    }
} 