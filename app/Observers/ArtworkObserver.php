<?php

namespace App\Observers;

use App\Models\Artwork;
use App\Services\CacheService;

class ArtworkObserver
{
    private CacheService $cacheService;

    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    /**
     * Handle the Artwork "created" event.
     */
    public function created(Artwork $artwork): void
    {
        $this->cacheService->invalidateArtworkCaches($artwork->id);
    }

    /**
     * Handle the Artwork "updated" event.
     */
    public function updated(Artwork $artwork): void
    {
        $this->cacheService->invalidateArtworkCaches($artwork->id);
    }

    /**
     * Handle the Artwork "deleted" event.
     */
    public function deleted(Artwork $artwork): void
    {
        $this->cacheService->invalidateArtworkCaches($artwork->id);
    }
}
