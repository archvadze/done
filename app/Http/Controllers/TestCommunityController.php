<?php

namespace App\Http\Controllers;

use App\Models\Community;
use Illuminate\Http\Request;

class TestCommunityController extends Controller
{
    public function test($slug)
    {
        try {
            $community = Community::where('slug', $slug)->firstOrFail();
            
            // Test each part separately
            echo "Community found: " . $community->name . "\n";
            
            // Test creator loading
            $community->load(['creator']);
            echo "Creator loaded: " . $community->creator->name . "\n";
            
            // Test activeMembers loading with the exact same query from the controller
            $community->load(['activeMembers' => function ($query) {
                $query->latest('community_members.joined_at')->limit(12);
            }]);
            echo "Active members with query loaded\n";
            
            // Test posts loading
            $postsQuery = $community->posts()
                ->with(['user', 'comments'])
                ->where('is_locked', false);
            
            $posts = $postsQuery->orderBy('is_pinned', 'desc')->orderBy('created_at', 'desc')->limit(5)->get();
            echo "Posts loaded: " . $posts->count() . " posts\n";
            
            return response("All tests passed for community: " . $community->name);
            
        } catch (\Exception $e) {
            return response("Error: " . $e->getMessage() . "\nFile: " . $e->getFile() . ":" . $e->getLine() . "\nTrace: " . $e->getTraceAsString(), 500);
        }
    }
}
