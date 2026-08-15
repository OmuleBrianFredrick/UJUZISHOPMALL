<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;

class ProductionReadinessCommand extends Command
{
    protected $signature = 'app:production-readiness';
    protected $description = 'Check critical production configuration and Phase 12 schema prerequisites.';

    public function handle(): int
    {
        $checks = [
            'APP_KEY is configured' => filled(config('app.key')),
            'APP_DEBUG is disabled' => config('app.debug') === false,
            'sessions table exists' => Schema::hasTable('sessions'),
            'cache table exists when database cache is selected' => config('cache.default') !== 'database' || Schema::hasTable('cache'),
            'wishlists table exists' => Schema::hasTable('wishlists'),
            'reviews table exists' => Schema::hasTable('reviews'),
            'promotions table exists' => Schema::hasTable('promotions'),
            'loyalty_transactions table exists' => Schema::hasTable('loyalty_transactions'),
        ];

        $failed = 0;
        foreach ($checks as $label => $passed) {
            $this->line(($passed ? '<fg=green>PASS</>' : '<fg=red>FAIL</>') . " {$label}");
            if (!$passed) $failed++;
        }

        if ($failed) {
            $this->error("Production readiness failed: {$failed} check(s) need attention.");
            return self::FAILURE;
        }

        $this->info('Production readiness checks passed.');
        return self::SUCCESS;
    }
}
