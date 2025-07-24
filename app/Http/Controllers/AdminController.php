<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Artwork;
use App\Models\Evaluation;

class AdminController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (!Auth::user()->isAdmin()) {
                abort(403, 'Access denied. Admin privileges required.');
            }
            return $next($request);
        });
    }

    /**
     * Display the admin dashboard
     */
    public function dashboard()
    {
        $stats = [
            'total_users' => User::count(),
            'total_artworks' => Artwork::count(),
            'total_evaluations' => Evaluation::count(),
            'recent_users' => User::latest()->limit(5)->get(),
            'recent_artworks' => Artwork::with('user')->latest()->limit(5)->get(),
            'recent_evaluations' => Evaluation::with(['artwork', 'evaluator'])->latest()->limit(5)->get(),
        ];

        return view('admin.dashboard', compact('stats'));
    }

    /**
     * Display users management page
     */
    public function users(Request $request)
    {
        $query = User::query();

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Role filter
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Status filter
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->whereNull('deleted_at');
            } elseif ($request->status === 'blocked') {
                $query->whereNotNull('deleted_at');
            }
        }

        $users = $query->withCount(['artworks', 'evaluations'])
                      ->latest()
                      ->paginate(20);

        return view('admin.users.index', compact('users'));
    }

    /**
     * Display artworks management page
     */
    public function artworks(Request $request)
    {
        $query = Artwork::with(['user']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Category filter
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $artworks = $query->withCount(['evaluations'])
                          ->latest()
                          ->paginate(20);

        return view('admin.artworks.index', compact('artworks'));
    }

    /**
     * Display evaluations management page
     */
    public function evaluations(Request $request)
    {
        $query = Evaluation::with(['artwork', 'evaluator']);

        // Source filter
        if ($request->filled('source')) {
            $query->where('source', $request->source);
        }

        // Status filter  
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $evaluations = $query->latest()
                            ->paginate(20);

        return view('admin.evaluations.index', compact('evaluations'));
    }

    /**
     * Display system settings page
     */
    public function settings()
    {
        return view('admin.settings');
    }

    /**
     * Display system logs
     */
    public function logs()
    {
        return view('admin.logs');
    }
}
