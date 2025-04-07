<?php

namespace App\Jobs;

use App\Models\Alert;
use App\Models\Mention;
use App\Services\AlertService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessAlertNotifications implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public Alert $alert,
        public Mention $mention
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $alert = $this->alert;
        $mention = $this->mention;
        
        // Get the alert service
        $alertService = app(AlertService::class);
        
        // Process the alert
        $alertService->processAlert($alert, $mention);
    }

    protected function shouldProcessAlert(): bool
    {
        if (!$this->alert->is_active) {
            return false;
        }

        if ($this->alert->last_triggered_at === null) {
            return true;
        }

        $frequency = $this->alert->notification_frequency;
        $lastTriggered = $this->alert->last_triggered_at;

        return match ($frequency) {
            'immediate' => true,
            'hourly' => $lastTriggered->addHour()->isPast(),
            'daily' => $lastTriggered->addDay()->isPast(),
            'weekly' => $lastTriggered->addWeek()->isPast(),
            default => false,
        };
    }

    public static function dispatchJob(Alert $alert, Mention $mention): mixed
    {
        if (!$alert->is_active) {
            return null;
        }

        return dispatch(new static($alert, $mention));
    }
} 