<?php

namespace App\Services;

use App\Models\Artwork;
use App\Models\Evaluation;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class CacheService
{
    /**
     * Cache duration constants (in minutes)
     */
    const ARTWORK_LIST_CACHE = 5;
    const LEADERBOARD_CACHE = 10;
    const USER_PROFILE_CACHE = 15;
    const ACQ_SCORE_CACHE = 30;
    const ARTWORK_DETAILS_CACHE = 20;

    /**
     * Cache keys
     */
    const ARTWORK_LIST_KEY = 'artworks:list:';
    const LEADERBOARD_KEY = 'leaderboard:';
    const USER_PROFILE_KEY = 'user:profile:';
    const ACQ_SCORE_KEY = 'artwork:acq:';
    const ARTWORK_DETAILS_KEY = 'artwork:details:';

    /**
     * Get or cache artwork list
     */
    public function getArtworkList(array $filters = [], int $page = 1, int $perPage = 15)
    {
        $cacheKey = self::ARTWORK_LIST_KEY . md5(serialize($filters) . $page . $perPage);
        
        return Cache::remember($cacheKey, self::ARTWORK_LIST_CACHE, function () use ($filters, $page, $perPage) {
            $query = Artwork::with(['user:id,name,avatar_path'])->published();

            // Apply filters
            if (!empty($filters['search'])) {
                $search = $filters['search'];
                $query->where(function ($q) use ($search) {
                    $q->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(title, '$.en')) LIKE ?", ["%{$search}%"])
                        ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(title, '$.ka')) LIKE ?", ["%{$search}%"])
                        ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(description, '$.en')) LIKE ?", ["%{$search}%"])
                        ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(description, '$.ka')) LIKE ?", ["%{$search}%"]);
                });
            }

            if (!empty($filters['category'])) {
                $query->where('category', $filters['category']);
            }

            if (!empty($filters['ai_generated'])) {
                $query->where('is_ai_generated', true);
            }

            return $query->orderBy('created_at', 'desc')
                        ->paginate($perPage, ['*'], 'page', $page);
        });
    }

    /**
     * Get or cache leaderboard
     */
    public function getLeaderboard(int $limit = 10)
    {
        $cacheKey = self::LEADERBOARD_KEY . $limit;
        
        return Cache::remember($cacheKey, self::LEADERBOARD_CACHE, function () use ($limit) {
            return Artwork::with(['user:id,name,avatar_path'])
                        ->published()
                        ->whereNotNull('acq_score')
                        ->orderBy('acq_score', 'desc')
                        ->limit($limit)
                        ->get();
        });
    }

    /**
     * Get or cache user profile data
     */
    public function getUserProfile(int $userId)
    {
        $cacheKey = self::USER_PROFILE_KEY . $userId;
        
        return Cache::remember($cacheKey, self::USER_PROFILE_CACHE, function () use ($userId) {
            $user = \App\Models\User::findOrFail($userId);
            
            return [
                'user' => $user,
                'artworks_count' => $user->artworks()->published()->count(),
                'evaluations_count' => $user->evaluations()->count(),
                'average_acq_score' => $user->artworks()
                    ->published()
                    ->whereNotNull('acq_score')
                    ->avg('acq_score'),
                'total_likes' => $user->artworks()
                    ->published()
                    ->sum('likes_count')
            ];
        });
    }

    /**
     * Get or cache artwork ACQ score
     */
    public function getArtworkAcqScore(int $artworkId)
    {
        $cacheKey = self::ACQ_SCORE_KEY . $artworkId;
        
        return Cache::remember($cacheKey, self::ACQ_SCORE_CACHE, function () use ($artworkId) {
            $evaluations = Evaluation::where('artwork_id', $artworkId)->get();
            
            if ($evaluations->isEmpty()) {
                return null;
            }

            $totalScore = 0;
            $count = 0;

            foreach ($evaluations as $evaluation) {
                $acqScore = (
                    $evaluation->originality_score * 0.25 +
                    $evaluation->technical_score * 0.25 +
                    $evaluation->aesthetic_score * 0.25 +
                    $evaluation->concept_score * 0.25
                ) * ($evaluation->overall_score / 10);

                $totalScore += $acqScore;
                $count++;
            }

            return round($totalScore / $count, 2);
        });
    }

    /**
     * Get or cache artwork details
     */
    public function getArtworkDetails(int $artworkId)
    {
        $cacheKey = self::ARTWORK_DETAILS_KEY . $artworkId;
        
        return Cache::remember($cacheKey, self::ARTWORK_DETAILS_CACHE, function () use ($artworkId) {
            return Artwork::with([
                'user:id,name,avatar_path',
                'evaluations.user:id,name',
                'category'
            ])->findOrFail($artworkId);
        });
    }

    /**
     * Invalidate artwork-related caches
     */
    public function invalidateArtworkCaches(int $artworkId = null)
    {
        // Clear artwork list caches
        $this->clearCacheByPattern(self::ARTWORK_LIST_KEY . '*');
        
        // Clear leaderboard cache
        $this->clearCacheByPattern(self::LEADERBOARD_KEY . '*');
        
        if ($artworkId) {
            // Clear specific artwork caches
            Cache::forget(self::ACQ_SCORE_KEY . $artworkId);
            Cache::forget(self::ARTWORK_DETAILS_KEY . $artworkId);
            
            // Clear user profile cache for artwork owner
            $artwork = Artwork::find($artworkId);
            if ($artwork) {
                Cache::forget(self::USER_PROFILE_KEY . $artwork->user_id);
            }
        }
    }

    /**
     * Invalidate user-related caches
     */
    public function invalidateUserCaches(int $userId)
    {
        Cache::forget(self::USER_PROFILE_KEY . $userId);
    }

    /**
     * Clear cache by pattern (Redis specific)
     */
    private function clearCacheByPattern(string $pattern)
    {
        if (config('cache.default') === 'redis') {
            $redis = app('redis');
            $keys = $redis->keys(config('cache.prefix', 'laravel_cache') . ':' . $pattern);
            
            if (!empty($keys)) {
                $redis->del($keys);
            }
        }
    }

    /**
     * Warm up critical caches
     */
    public function warmUpCaches()
    {
        // Warm up leaderboard
        $this->getLeaderboard();
        
        // Warm up first page of artworks
        $this->getArtworkList();
        
        // Warm up top users
        $topUsers = \App\Models\User::withCount('artworks')
                                   ->orderBy('artworks_count', 'desc')
                                   ->limit(10)
                                   ->pluck('id');
        
        foreach ($topUsers as $userId) {
            $this->getUserProfile($userId);
        }
    }

    /**
     * Get cache statistics
     */
    public function getCacheStats()
    {
        if (config('cache.default') === 'redis') {
            $redis = app('redis');
            $info = $redis->info();
            
            return [
                'connected_clients' => $info['connected_clients'] ?? 0,
                'used_memory_human' => $info['used_memory_human'] ?? '0B',
                'keyspace_hits' => $info['keyspace_hits'] ?? 0,
                'keyspace_misses' => $info['keyspace_misses'] ?? 0,
                'total_commands_processed' => $info['total_commands_processed'] ?? 0,
            ];
        }
        
        return ['status' => 'Cache driver is not Redis'];
    }
}
