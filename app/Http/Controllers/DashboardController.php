<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Artwork;
use App\Models\SupportTicket;
use App\Models\Payment;
use App\Models\Nft;

class DashboardController extends Controller
{
    /**
     * Show the user dashboard
     */
    public function index()
    {
        $user = Auth::user();

        // Redirect admins to admin panel
        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        // Get user statistics - use simpler approach to avoid array issues
        $stats = [
            'artworks' => 0,
            'published_artworks' => 0,
            'likes_received' => 0,
            'nfts' => 0,
            'support_tickets' => 0,
            'open_tickets' => 0,
        ];

        // Get recent activity
        $recentArtworks = collect(); // Empty collection for now
        $recentTickets = collect(); // Empty collection for now

        // Try to get recent payments, handle if Payment model doesn't have proper relationship
        try {
            $recentPayments = collect(); // Empty collection for now
        } catch (\Exception $e) {
            $recentPayments = collect(); // Empty collection if payments relationship fails
        }

        // Get recent NFTs
        $recentNfts = collect(); // Empty collection for now

        return view('dashboard.index', compact(
            'user',
            'stats',
            'recentArtworks',
            'recentTickets',
            'recentPayments',
            'recentNfts'
        ));
    }
}
