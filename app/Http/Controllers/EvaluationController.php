<?php

namespace App\Http\Controllers;

use App\Models\Artwork;
use App\Models\Evaluation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class EvaluationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['index', 'show', 'leaderboard']);
    }

    /**
     * Display evaluations for an artwork
     */
    public function index(Request $request, Artwork $artwork)
    {
        $evaluations = $artwork->evaluations()
            ->with('evaluator')
            ->latest()
            ->paginate(10);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'evaluations' => $evaluations,
                'artwork_acq_score' => $artwork->acq_score,
                'evaluation_count' => $artwork->evaluation_count,
            ]);
        }

        return view('evaluations.index', compact('artwork', 'evaluations'));
    }

    /**
     * Show the form for creating a new evaluation
     */
    public function create(Artwork $artwork)
    {
        // Check if user already evaluated this artwork
        $existingEvaluation = Evaluation::where('artwork_id', $artwork->id)
            ->where('evaluator_id', Auth::id())
            ->first();

        if ($existingEvaluation) {
            return redirect()
                ->route('artworks.show', $artwork)
                ->with('error', 'You have already evaluated this artwork.');
        }

        // Don't allow self-evaluation
        if ($artwork->user_id === Auth::id()) {
            abort(403, 'You cannot evaluate your own artwork.');
        }

        return view('evaluations.create', compact('artwork'));
    }

    /**
     * Store a newly created evaluation
     */
    public function store(Request $request, Artwork $artwork)
    {
        // Validate request (exclude artwork_id since it comes from route)
        $rules = Evaluation::validationRules();
        unset($rules['artwork_id']); // Remove artwork_id validation since it's from route
        $validated = $request->validate($rules);

        // Check for existing evaluation
        $existingEvaluation = Evaluation::where('artwork_id', $artwork->id)
            ->where('evaluator_id', Auth::id())
            ->first();

        if ($existingEvaluation) {
            throw ValidationException::withMessages([
                'artwork' => 'You have already evaluated this artwork.'
            ]);
        }

        // Don't allow self-evaluation
        if ($artwork->user_id === Auth::id()) {
            abort(403, 'You cannot evaluate your own artwork.');
        }

        try {
            DB::beginTransaction();

            // Create evaluation
            $evaluation = Evaluation::create([
                'artwork_id' => $artwork->id,
                'evaluator_id' => Auth::id(),
                'score_technique' => $validated['score_technique'],
                'score_composition' => $validated['score_composition'],
                'score_originality' => $validated['score_originality'],
                'score_impact' => $validated['score_impact'],
                'feedback_text' => $validated['feedback_text'] ?? null,
                'source' => 'human',
                'status' => 'approved', // Auto-approve human evaluations for now
            ]);

            // Recalculate artwork's ACQ score
            $artwork->calculateAcqScore();

            DB::commit();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Evaluation submitted successfully!',
                    'evaluation' => $evaluation->load('evaluator'),
                    'new_acq_score' => $artwork->fresh()->acq_score,
                ]);
            }

            return redirect()
                ->route('artworks.show', $artwork)
                ->with('success', 'Thank you for your evaluation!');
        } catch (\Exception $e) {
            DB::rollback();
            
            // Log the actual error for debugging
            Log::error('Failed to submit evaluation: ' . $e->getMessage(), [
                'exception' => $e,
                'artwork_id' => $artwork->id,
                'user_id' => Auth::id(),
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to submit evaluation: ' . $e->getMessage()
                ], 500);
            }

            return back()
                ->withErrors(['error' => 'Failed to submit evaluation: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Display the specified evaluation
     */
    public function show(Artwork $artwork, Evaluation $evaluation)
    {
        $this->authorize('view', $evaluation);

        return view('evaluations.show', compact('artwork', 'evaluation'));
    }

    /**
     * Show the form for editing the specified evaluation
     */
    public function edit(Artwork $artwork, Evaluation $evaluation)
    {
        // Only allow editing own evaluations
        if ($evaluation->evaluator_id !== Auth::id()) {
            abort(403, 'Unauthorized to edit this evaluation.');
        }

        return view('evaluations.edit', compact('artwork', 'evaluation'));
    }

    /**
     * Update the specified evaluation
     */
    public function update(Request $request, Artwork $artwork, Evaluation $evaluation)
    {
        // Only allow editing own evaluations
        if ($evaluation->evaluator_id !== Auth::id()) {
            abort(403, 'Unauthorized to edit this evaluation.');
        }

        // Validate request (exclude artwork_id and source since they come from context)
        $rules = Evaluation::validationRules();
        unset($rules['artwork_id'], $rules['source']); // Remove fields that come from context
        $validated = $request->validate($rules);

        try {
            DB::beginTransaction();

            $evaluation->update([
                'score_technique' => $validated['score_technique'],
                'score_composition' => $validated['score_composition'],
                'score_originality' => $validated['score_originality'],
                'score_impact' => $validated['score_impact'],
                'feedback_text' => $validated['feedback_text'] ?? null,
            ]);

            // Recalculate artwork's ACQ score
            $artwork->calculateAcqScore();

            DB::commit();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Evaluation updated successfully!',
                    'evaluation' => $evaluation->fresh()->load('evaluator'),
                    'new_acq_score' => $artwork->fresh()->acq_score,
                ]);
            }

            return redirect()
                ->route('artworks.show', $artwork)
                ->with('success', 'Evaluation updated successfully!');
        } catch (\Exception $e) {
            DB::rollback();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update evaluation: ' . $e->getMessage()
                ], 500);
            }

            return back()
                ->withErrors(['error' => 'Failed to update evaluation.'])
                ->withInput();
        }
    }

    /**
     * Remove the specified evaluation
     */
    public function destroy(Artwork $artwork, Evaluation $evaluation)
    {
        // Only allow deleting own evaluations or if admin
        if ($evaluation->evaluator_id !== Auth::id() && Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized to delete this evaluation.');
        }

        try {
            DB::beginTransaction();

            $evaluation->delete();

            // Recalculate artwork's ACQ score
            $artwork->calculateAcqScore();

            DB::commit();

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Evaluation deleted successfully!',
                    'new_acq_score' => $artwork->fresh()->acq_score,
                ]);
            }

            return redirect()
                ->route('artworks.show', $artwork)
                ->with('success', 'Evaluation deleted successfully!');
        } catch (\Exception $e) {
            DB::rollback();

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to delete evaluation: ' . $e->getMessage()
                ], 500);
            }

            return back()
                ->withErrors(['error' => 'Failed to delete evaluation.']);
        }
    }

    /**
     * Get top rated artworks (leaderboard)
     */
    public function leaderboard(Request $request)
    {
        $timeframe = $request->get('timeframe', 'all'); // all, week, month, year

        $query = Artwork::where('status', 'published')
            ->whereNotNull('acq_score')
            ->where('evaluation_count', '>', 0)
            ->with(['user', 'evaluations' => function ($q) {
                $q->approved()->latest()->limit(3);
            }]);

        // Apply timeframe filter
        switch ($timeframe) {
            case 'week':
                $query->where('created_at', '>=', now()->subWeek());
                break;
            case 'month':
                $query->where('created_at', '>=', now()->subMonth());
                break;
            case 'year':
                $query->where('created_at', '>=', now()->subYear());
                break;
        }

        $topArtworks = $query->orderBy('acq_score', 'desc')
            ->paginate(20);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'artworks' => $topArtworks,
                'timeframe' => $timeframe,
            ]);
        }

        return view('evaluations.leaderboard', compact('topArtworks', 'timeframe'));
    }
}
