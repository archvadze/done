<?php

namespace App\Observers;

use App\Models\Evaluation;
use App\Services\CacheService;

class EvaluationObserver
{
    private CacheService $cacheService;

    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    /**
     * Handle the Evaluation "created" event.
     */
    public function created(Evaluation $evaluation): void
    {
        // Invalidate artwork and leaderboard caches
        $this->cacheService->invalidateArtworkCaches($evaluation->artwork_id);
    }

    /**
     * Handle the Evaluation "updated" event.
     */
    public function updated(Evaluation $evaluation): void
    {
        // Invalidate artwork and leaderboard caches
        $this->cacheService->invalidateArtworkCaches($evaluation->artwork_id);
    }

    /**
     * Handle the Evaluation "deleted" event.
     */
    public function deleted(Evaluation $evaluation): void
    {
        // Invalidate artwork and leaderboard caches
        $this->cacheService->invalidateArtworkCaches($evaluation->artwork_id);
    }
}
