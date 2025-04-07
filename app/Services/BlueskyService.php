<?php

namespace App\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

class BlueskyService
{
    private string $baseUrl;
    private ?string $accessJwt = null;
    private const RATE_LIMIT_KEY = 'bluesky:rate_limit:';
    private const RATE_LIMIT_WINDOW = 60; // 1 minute
    private const MAX_REQUESTS = 30; // 30 requests per minute

    public function __construct()
    {
        $this->baseUrl = config('services.bluesky.api_url', 'https://bsky.social/xrpc');
    }

    /**
     * Check if we're within rate limits
     */
    private function checkRateLimit(): bool
    {
        $key = self::RATE_LIMIT_KEY . date('Y-m-d-H');
        $current = Redis::incr($key);
        
        if ($current === 1) {
            Redis::expire($key, self::RATE_LIMIT_WINDOW);
        }
        
        return $current <= self::MAX_REQUESTS;
    }

    /**
     * Create an authenticated HTTP client
     */
    private function client(): PendingRequest
    {
        if (!$this->checkRateLimit()) {
            throw new \Exception('Rate limit exceeded. Please try again later.');
        }

        $client = Http::baseUrl($this->baseUrl)
            ->timeout(30)
            ->withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ]);

        if ($this->accessJwt) {
            $client->withHeader('Authorization', 'Bearer ' . $this->accessJwt);
        }

        return $client;
    }

    /**
     * Authenticate with Bluesky
     */
    public function authenticate(string $identifier, string $password): bool
    {
        try {
            $response = $this->client()
                ->post('/com.atproto.server.createSession', [
                    'identifier' => $identifier,
                    'password' => $password,
                ]);

            if ($response->successful()) {
                $data = $response->json();
                $this->accessJwt = $data['accessJwt'];
                return true;
            }

            return false;
        } catch (\Exception $e) {
            report($e);
            return false;
        }
    }

    /**
     * Search for posts containing specific text
     */
    public function searchPosts(string $query, ?string $cursor = null): array
    {
        try {
            $response = $this->client()
                ->get('/app.bsky.feed.searchPosts', [
                    'q' => $query,
                    'limit' => 50,
                    'cursor' => $cursor,
                ]);

            if ($response->successful()) {
                return $response->json();
            }

            return [];
        } catch (\Exception $e) {
            report($e);
            return [];
        }
    }

    /**
     * Get post details by URI
     */
    public function getPost(string $uri): ?array
    {
        try {
            $response = $this->client()
                ->get('/app.bsky.feed.getPostThread', [
                    'uri' => $uri,
                ]);

            if ($response->successful()) {
                return $response->json();
            }

            return null;
        } catch (\Exception $e) {
            report($e);
            return null;
        }
    }

    /**
     * Get user profile by handle
     */
    public function getProfile(string $handle): ?array
    {
        try {
            $response = $this->client()
                ->get('/app.bsky.actor.getProfile', [
                    'actor' => $handle,
                ]);

            if ($response->successful()) {
                return $response->json();
            }

            return null;
        } catch (\Exception $e) {
            report($e);
            return null;
        }
    }

    /**
     * Search for mentions of a user
     */
    public function searchMentions(string $handle, array $keywords = []): array
    {
        // Build search query
        $query = "@{$handle}";
        if (!empty($keywords)) {
            $query .= ' ' . implode(' OR ', $keywords);
        }
        
        // Search for posts
        $response = $this->client()
            ->post('/app.bsky.feed.searchPosts', [
                'json' => [
                    'q' => $query,
                    'limit' => 100,
                ],
            ]);
        
        $data = json_decode($response->getBody(), true);
        
        // Process posts
        $mentions = [];
        foreach ($data['posts'] as $post) {
            // Skip if post is from the user themselves
            if ($post['author']['handle'] === $handle) {
                continue;
            }
            
            // Skip if post doesn't mention the user
            if (!str_contains(strtolower($post['text']), strtolower("@{$handle}"))) {
                continue;
            }
            
            // Skip if post doesn't contain any keywords
            if (!empty($keywords)) {
                $containsKeyword = false;
                foreach ($keywords as $keyword) {
                    if (str_contains(strtolower($post['text']), strtolower($keyword))) {
                        $containsKeyword = true;
                        break;
                    }
                }
                if (!$containsKeyword) {
                    continue;
                }
            }
            
            $mentions[] = [
                'post_id' => $post['uri'],
                'author_handle' => $post['author']['handle'],
                'text' => $post['text'],
                'post_url' => "https://bsky.app/profile/{$post['author']['handle']}/post/{$post['uri']}",
                'post_indexed_at' => $post['indexedAt'],
            ];
        }
        
        return $mentions;
    }
} 