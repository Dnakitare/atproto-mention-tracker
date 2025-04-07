<?php

namespace App\Services;

use App\Models\User;
use App\Models\Mention;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class StatisticsService
{
    /**
     * Get mention frequency over time
     */
    public function getMentionFrequency(User $user, string $period = 'daily'): Collection
    {
        $query = $user->mentions()
            ->select(
                DB::raw('COUNT(*) as count'),
                DB::raw(match ($period) {
                    'hourly' => 'DATE_FORMAT(post_indexed_at, "%Y-%m-%d %H:00") as period',
                    'daily' => 'DATE(post_indexed_at) as period',
                    'weekly' => 'DATE(DATE_SUB(post_indexed_at, INTERVAL WEEKDAY(post_indexed_at) DAY)) as period',
                    'monthly' => 'DATE_FORMAT(post_indexed_at, "%Y-%m-01") as period',
                    default => 'DATE(post_indexed_at) as period'
                })
            )
            ->groupBy('period')
            ->orderBy('period', 'desc');

        if ($period === 'hourly') {
            $query->where('post_indexed_at', '>=', now()->subDay());
        } elseif ($period === 'daily') {
            $query->where('post_indexed_at', '>=', now()->subMonth());
        } elseif ($period === 'weekly') {
            $query->where('post_indexed_at', '>=', now()->subMonths(3));
        } elseif ($period === 'monthly') {
            $query->where('post_indexed_at', '>=', now()->subYear());
        }

        return $query->get();
    }

    /**
     * Get most active mentioners
     */
    public function getMostActiveMentioners(User $user, int $limit = 10): Collection
    {
        return $user->mentions()
            ->select('author_handle', DB::raw('COUNT(*) as mention_count'))
            ->groupBy('author_handle')
            ->orderByDesc('mention_count')
            ->limit($limit)
            ->get();
    }

    /**
     * Get popular keywords
     */
    public function getPopularKeywords(User $user, int $limit = 10): Collection
    {
        return $user->trackedKeywords()
            ->withCount(['mentions' => function ($query) {
                $query->where('post_indexed_at', '>=', now()->subMonth());
            }])
            ->orderByDesc('mentions_count')
            ->limit($limit)
            ->get();
    }

    /**
     * Get engagement metrics
     */
    public function getEngagementMetrics(User $user): array
    {
        $lastMonth = now()->subMonth();
        
        $totalMentions = $user->mentions()
            ->where('post_indexed_at', '>=', $lastMonth)
            ->count();

        $uniqueMentioners = $user->mentions()
            ->where('post_indexed_at', '>=', $lastMonth)
            ->distinct('author_handle')
            ->count('author_handle');

        $averageDailyMentions = $user->mentions()
            ->where('post_indexed_at', '>=', $lastMonth)
            ->select(DB::raw('COUNT(*) / 30.0 as avg_daily'))
            ->first()
            ->avg_daily;

        return [
            'total_mentions' => $totalMentions,
            'unique_mentioners' => $uniqueMentioners,
            'average_daily_mentions' => round($averageDailyMentions, 2),
            'tracked_keywords' => $user->trackedKeywords()->count(),
        ];
    }

    /**
     * Get mention statistics for a user
     */
    public function getMentionStatistics(User $user, ?Carbon $startDate = null, ?Carbon $endDate = null): array
    {
        // Get mentions
        $mentions = $user->mentions()
            ->when($startDate, function ($query) use ($startDate) {
                return $query->where('created_at', '>=', $startDate);
            })
            ->when($endDate, function ($query) use ($endDate) {
                return $query->where('created_at', '<=', $endDate);
            })
            ->get();
        
        // Calculate statistics
        $totalMentions = $mentions->count();
        $sentimentDistribution = $mentions->groupBy('sentiment')->map->count();
        $mentionsByDay = $mentions->groupBy(function ($mention) {
            return $mention->created_at->format('Y-m-d');
        })->map->count();
        
        // Calculate average sentiment
        $averageSentiment = $mentions->avg('sentiment') ?? 0;
        
        // Calculate top authors
        $topAuthors = $mentions->groupBy('author_handle')
            ->map(function ($group) {
                return [
                    'count' => $group->count(),
                    'average_sentiment' => $group->avg('sentiment'),
                ];
            })
            ->sortByDesc('count')
            ->take(10);
        
        return [
            'total_mentions' => $totalMentions,
            'sentiment_distribution' => $sentimentDistribution,
            'mentions_by_day' => $mentionsByDay,
            'average_sentiment' => $averageSentiment,
            'top_authors' => $topAuthors,
        ];
    }
} 