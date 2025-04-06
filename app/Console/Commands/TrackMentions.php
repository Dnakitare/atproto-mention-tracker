<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\BlueskyService;
use App\Services\MentionTrackingService;
use Illuminate\Console\Command;

class TrackMentions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mentions:track {--user=} {--all}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Track mentions for users';

    /**
     * Execute the console command.
     */
    public function handle(BlueskyService $blueskyService, MentionTrackingService $mentionTrackingService): void
    {
        // Authenticate with Bluesky
        $blueskyService->authenticate(
            config('services.bluesky.username'),
            config('services.bluesky.password')
        );

        if ($userId = $this->option('user')) {
            $user = User::find($userId);
            if ($user) {
                $this->trackMentionsForUser($user, $mentionTrackingService);
            } else {
                $this->error("User not found with ID: {$userId}");
            }
            return;
        }

        if ($this->option('all')) {
            $users = User::whereHas('trackedKeywords', function ($query) {
                $query->where('is_active', true);
            })->get();

            $count = 0;
            foreach ($users as $user) {
                $this->trackMentionsForUser($user, $mentionTrackingService);
                $count++;
            }

            $this->info("Tracked mentions for {$count} users.");
            return;
        }

        $this->error('Please specify either --user=ID or --all option.');
    }

    private function trackMentionsForUser(User $user, MentionTrackingService $mentionTrackingService): void
    {
        $this->info("Tracking mentions for user: {$user->name}");
        $mentionTrackingService->trackMentionsForUser($user);
        $this->info('Done.');
    }
}
