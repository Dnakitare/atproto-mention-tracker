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

    public function __construct(BlueskyService $blueskyService)
    {
        $this->blueskyService = $blueskyService;
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
        Mention::create([
            'user_id' => $user->id,
            'post_id' => $post['uri'],
            'author_did' => $post['author']['did'],
            'author_handle' => $post['author']['handle'],
            'post_text' => $post['record']['text'],
            'post_data' => $post,
            'post_indexed_at' => Carbon::parse($post['indexedAt']),
        ]);
    }

    /**
     * Get recent mentions for a user
     */
    public function getRecentMentions(User $user, int $limit = 50): Collection
    {
        return $user->mentions()
            ->orderBy('post_indexed_at', 'desc')
            ->limit($limit)
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
} 