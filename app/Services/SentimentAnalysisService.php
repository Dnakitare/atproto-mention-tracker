<?php

namespace App\Services;

use App\Models\Mention;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class SentimentAnalysisService
{
    private array $positiveWords = [
        'great', 'awesome', 'amazing', 'excellent', 'good', 'love', 'wonderful', 
        'fantastic', 'brilliant', 'perfect', 'best', 'happy', 'glad', 'pleased',
        'thank', 'thanks', 'appreciate', 'helpful', 'useful', 'beneficial'
    ];
    
    private array $negativeWords = [
        'bad', 'terrible', 'awful', 'horrible', 'worst', 'hate', 'disappointing',
        'poor', 'unhappy', 'sad', 'angry', 'frustrated', 'annoyed', 'upset',
        'problem', 'issue', 'wrong', 'broken', 'fail', 'failure'
    ];

    /**
     * Analyze the sentiment of a text
     */
    public function analyzeSentiment(string $text): float
    {
        // Remove URLs
        $text = preg_replace('/https?:\/\/\S+/', '', $text);
        
        // Remove mentions
        $text = preg_replace('/@\w+/', '', $text);
        
        // Remove hashtags
        $text = preg_replace('/#\w+/', '', $text);
        
        // Remove special characters
        $text = preg_replace('/[^\p{L}\p{N}\s]/u', '', $text);
        
        // Convert to lowercase
        $text = strtolower($text);
        
        // Split into words
        $words = explode(' ', $text);
        
        // Remove stop words
        $words = array_diff($words, $this->stopWords);
        
        // Calculate sentiment score
        $score = 0;
        $count = 0;
        
        foreach ($words as $word) {
            if (isset($this->sentimentScores[$word])) {
                $score += $this->sentimentScores[$word];
                $count++;
            }
        }
        
        // Return average sentiment score
        return $count > 0 ? $score / $count : 0;
    }
    
    /**
     * Get the sentiment label for a score
     */
    public function getSentimentLabel(float $score): string
    {
        if ($score >= 0.5) {
            return 'positive';
        } elseif ($score <= -0.5) {
            return 'negative';
        } else {
            return 'neutral';
        }
    }

    /**
     * Get sentiment trends for a user
     */
    public function getSentimentTrends(int $userId, int $days = 30): array
    {
        $cacheKey = "sentiment_trends_{$userId}_{$days}";
        
        return Cache::remember($cacheKey, now()->addHours(1), function () use ($userId, $days) {
            $mentions = Mention::where('user_id', $userId)
                ->where('post_indexed_at', '>=', now()->subDays($days))
                ->get();
                
            $sentiments = [
                'positive' => 0,
                'negative' => 0,
                'neutral' => 0,
            ];
            
            foreach ($mentions as $mention) {
                $sentiment = $this->analyzeSentiment($mention->post_text);
                $sentimentLabel = $this->getSentimentLabel($sentiment);
                $sentiments[$sentimentLabel]++;
            }
            
            $total = array_sum($sentiments);
            
            if ($total > 0) {
                $sentiments['positive_percentage'] = round(($sentiments['positive'] / $total) * 100, 2);
                $sentiments['negative_percentage'] = round(($sentiments['negative'] / $total) * 100, 2);
                $sentiments['neutral_percentage'] = round(($sentiments['neutral'] / $total) * 100, 2);
            }
            
            return $sentiments;
        });
    }

    /**
     * Detect sentiment spikes
     */
    public function detectSentimentSpikes(int $userId, string $sentiment = 'negative', int $threshold = 3): Collection
    {
        $mentions = Mention::where('user_id', $userId)
            ->where('post_indexed_at', '>=', now()->subDay())
            ->get();
            
        $spikes = collect();
        $sentimentCount = 0;
        
        foreach ($mentions as $mention) {
            $sentiment = $this->analyzeSentiment($mention->post_text);
            $sentimentLabel = $this->getSentimentLabel($sentiment);
            
            if ($sentimentLabel === $sentiment) {
                $sentimentCount++;
                
                if ($sentimentCount >= $threshold) {
                    $spikes->push([
                        'mention' => $mention,
                        'count' => $sentimentCount,
                        'time' => $mention->post_indexed_at,
                    ]);
                }
            } else {
                $sentimentCount = 0;
            }
        }
        
        return $spikes;
    }
} 