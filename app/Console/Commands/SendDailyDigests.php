<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Console\Command;

class SendDailyDigests extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mentions:send-daily-digests';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send daily digest emails to users';

    /**
     * Execute the console command.
     */
    public function handle(NotificationService $notificationService): void
    {
        $users = User::whereHas('notificationSetting', function ($query) {
            $query->where('daily_digest', true);
        })->get();

        $count = 0;
        foreach ($users as $user) {
            $notificationService->sendDailyDigest($user);
            $count++;
        }

        $this->info("Sent daily digests to {$count} users.");
    }
} 