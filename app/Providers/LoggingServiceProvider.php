<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Monolog\Processor\ProcessorInterface;

class LoggingServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Log::pushProcessor(function ($record) {
            $record['extra'] = array_merge($record['extra'] ?? [], [
                'environment' => config('app.env'),
                'app_version' => config('app.version'),
                'request_id' => request()->header('X-Request-ID') ?? Str::uuid()->toString(),
                'user_id' => auth()->id(),
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            return $record;
        });
    }
} 