<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\CacheService;

class WarmUpCache extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'cache:warmup
                            {--clear : Clear existing cache before warming up}';

    /**
     * The console command description.
     */
    protected $description = 'Warm up application cache for better performance';

    private CacheService $cacheService;

    public function __construct(CacheService $cacheService)
    {
        parent::__construct();
        $this->cacheService = $cacheService;
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('🔥 Starting cache warm-up...');

        if ($this->option('clear')) {
            $this->info('🧹 Clearing existing cache...');
            $this->call('cache:clear');
        }

        // Warm up critical caches
        $this->info('🔥 Warming up critical caches...');
        $this->cacheService->warmUpCaches();

        // Show cache statistics
        $this->info('📊 Cache Statistics:');
        $stats = $this->cacheService->getCacheStats();

        if (isset($stats['status'])) {
            $this->warn($stats['status']);
        } else {
            $this->table(
                ['Metric', 'Value'],
                [
                    ['Connected Clients', $stats['connected_clients']],
                    ['Memory Used', $stats['used_memory_human']],
                    ['Cache Hits', $stats['keyspace_hits']],
                    ['Cache Misses', $stats['keyspace_misses']],
                    ['Commands Processed', $stats['total_commands_processed']],
                ]
            );
        }

        $this->info('✅ Cache warm-up completed successfully!');

        return Command::SUCCESS;
    }
}
