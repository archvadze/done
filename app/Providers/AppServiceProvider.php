<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Artwork;
use App\Models\Evaluation;
use App\Observers\ArtworkObserver;
use App\Observers\EvaluationObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register model observers for cache invalidation
        Artwork::observe(ArtworkObserver::class);
        Evaluation::observe(EvaluationObserver::class);
    }
}
