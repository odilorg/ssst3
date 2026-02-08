<?php

namespace App\Console\Commands;

use Filament\Notifications\Notification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CheckFailedJobs extends Command
{
    protected $signature = 'queue:check-failed';
    protected $description = 'Alert if new failed jobs exist (runs on schedule)';

    public function handle(): int
    {
        $currentCount = DB::table('failed_jobs')->count();
        $lastKnownCount = (int) Cache::get('failed_jobs_last_count', 0);

        if ($currentCount > $lastKnownCount) {
            $newFailures = $currentCount - $lastKnownCount;

            // Get details of recent failures
            $recent = DB::table('failed_jobs')
                ->orderByDesc('failed_at')
                ->limit($newFailures)
                ->get(['uuid', 'queue', 'exception', 'failed_at']);

            $summary = $recent->map(function ($job) {
                $firstLine = explode("\n", $job->exception)[0] ?? 'Unknown error';
                return "- [{$job->queue}] {$firstLine}";
            })->implode("\n");

            Log::error("Queue alert: {$newFailures} new failed job(s)", [
                'new_failures' => $newFailures,
                'total_failed' => $currentCount,
            ]);

            // Notify admin user in Filament
            $admin = \App\Models\User::first();
            if ($admin) {
                Notification::make()
                    ->danger()
                    ->title("{$newFailures} Failed Queue Job(s)")
                    ->body("Check failed_jobs table. Latest:\n{$summary}")
                    ->persistent()
                    ->sendToDatabase($admin);
            }

            $this->error("{$newFailures} new failed job(s) detected!");
        } else {
            $this->info("No new failed jobs (total: {$currentCount}).");
        }

        Cache::put('failed_jobs_last_count', $currentCount, now()->addDay());

        return self::SUCCESS;
    }
}
