<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Artwork;
use App\Models\Evaluation;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Services\CacheService;
use Exception;

class EvaluationApiController extends Controller
{
    private CacheService $cacheService;

    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }
    /**
     * Display evaluations for an artwork
     */
    public function index(Artwork $artwork): JsonResponse
    {
        $evaluations = $artwork->evaluations()
            ->where('status', 'approved')
            ->with('evaluator:id,name,avatar_path')
            ->latest()
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $evaluations->items(),
            'pagination' => [
                'current_page' => $evaluations->currentPage(),
                'last_page' => $evaluations->lastPage(),
                'per_page' => $evaluations->perPage(),
                'total' => $evaluations->total(),
                'has_more' => $evaluations->hasMorePages(),
            ],
            'artwork' => [
                'id' => $artwork->id,
                'title' => $artwork->getTitle(),
                'acq_score' => $artwork->acq_score,
                'evaluation_count' => $artwork->evaluation_count,
            ]
        ]);
    }

    /**
     * Store a new evaluation
     */
    public function store(Request $request, Artwork $artwork): JsonResponse
    {
        $user = Auth::user();

        // Check if user can evaluate this artwork
        if ($artwork->user_id === $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot evaluate your own artwork'
            ], 403);
        }

        // Check if user has already evaluated this artwork
        $existingEvaluation = $artwork->evaluations()
            ->where('evaluator_id', $user->id)
            ->first();

        if ($existingEvaluation) {
            return response()->json([
                'success' => false,
                'message' => 'You have already evaluated this artwork. Use PUT to update your evaluation.'
            ], 409);
        }

        $validator = Validator::make($request->all(), [
            'technique_score' => 'required|integer|min:1|max:10',
            'composition_score' => 'required|integer|min:1|max:10',
            'originality_score' => 'required|integer|min:1|max:10',
            'impact_score' => 'required|integer|min:1|max:10',
            'feedback_text' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Create the evaluation
            $evaluation = $artwork->evaluations()->create([
                'evaluator_id' => $user->id,
                'technique_score' => $request->technique_score,
                'composition_score' => $request->composition_score,
                'originality_score' => $request->originality_score,
                'impact_score' => $request->impact_score,
                'feedback_text' => $request->feedback_text,
                'status' => 'approved', // Auto-approve for now
            ]);

            // Recalculate ACQ score
            $artwork->calculateAcqScore();

            return response()->json([
                'success' => true,
                'message' => 'Evaluation submitted successfully!',
                'data' => [
                    'evaluation_id' => $evaluation->id,
                    'artwork_new_acq_score' => $artwork->fresh()->acq_score,
                    'evaluation_count' => $artwork->fresh()->evaluation_count,
                ]
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to submit evaluation: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified evaluation
     */
    public function show(Evaluation $evaluation): JsonResponse
    {
        $evaluation->load([
            'evaluator:id,name,avatar_path',
            'artwork:id,title,user_id'
        ]);

        // Check if evaluation is accessible
        if ($evaluation->status !== 'approved' && $evaluation->evaluator_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Evaluation not found or not accessible'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $evaluation->id,
                'technique_score' => $evaluation->technique_score,
                'composition_score' => $evaluation->composition_score,
                'originality_score' => $evaluation->originality_score,
                'impact_score' => $evaluation->impact_score,
                'average_score' => $evaluation->average_score,
                'feedback_text' => $evaluation->feedback_text,
                'status' => $evaluation->status,
                'created_at' => $evaluation->created_at,
                'updated_at' => $evaluation->updated_at,
                'evaluator' => $evaluation->evaluator,
                'artwork' => [
                    'id' => $evaluation->artwork->id,
                    'title' => $evaluation->artwork->getTitle(),
                ],
                'can_edit' => Auth::check() && Auth::id() === $evaluation->evaluator_id,
            ]
        ]);
    }

    /**
     * Update the specified evaluation
     */
    public function update(Request $request, Evaluation $evaluation): JsonResponse
    {
        $user = Auth::user();

        // Check if user owns this evaluation
        if ($evaluation->evaluator_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'You can only edit your own evaluations'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'technique_score' => 'required|integer|min:1|max:10',
            'composition_score' => 'required|integer|min:1|max:10',
            'originality_score' => 'required|integer|min:1|max:10',
            'impact_score' => 'required|integer|min:1|max:10',
            'feedback_text' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $evaluation->update([
                'technique_score' => $request->technique_score,
                'composition_score' => $request->composition_score,
                'originality_score' => $request->originality_score,
                'impact_score' => $request->impact_score,
                'feedback_text' => $request->feedback_text,
            ]);

            // Recalculate ACQ score for the artwork
            $evaluation->artwork->calculateAcqScore();

            return response()->json([
                'success' => true,
                'message' => 'Evaluation updated successfully!',
                'data' => [
                    'evaluation_id' => $evaluation->id,
                    'average_score' => $evaluation->average_score,
                    'artwork_new_acq_score' => $evaluation->artwork->fresh()->acq_score,
                ]
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update evaluation: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified evaluation
     */
    public function destroy(Evaluation $evaluation): JsonResponse
    {
        $user = Auth::user();

        // Check if user owns this evaluation
        if ($evaluation->evaluator_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'You can only delete your own evaluations'
            ], 403);
        }

        try {
            $artwork = $evaluation->artwork;
            $evaluation->delete();

            // Recalculate ACQ score for the artwork
            $artwork->calculateAcqScore();

            return response()->json([
                'success' => true,
                'message' => 'Evaluation deleted successfully!',
                'data' => [
                    'artwork_new_acq_score' => $artwork->fresh()->acq_score,
                    'evaluation_count' => $artwork->fresh()->evaluation_count,
                ]
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete evaluation: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get ACQ leaderboard
     */
    public function leaderboard(Request $request): JsonResponse
    {
        $limit = min($request->input('limit', 20), 50);
        
        $topArtworks = $this->cacheService->getLeaderboard($limit);

        return response()->json([
            'success' => true,
            'data' => $topArtworks->map(function($artwork) {
                return [
                    'id' => $artwork->id,
                    'title_en' => $artwork->title_en,
                    'title_ka' => $artwork->title_ka,
                    'acq_score' => $artwork->acq_score,
                    'likes_count' => $artwork->likes_count,
                    'file_path' => $artwork->file_path,
                    'user' => [
                        'id' => $artwork->user->id,
                        'name' => $artwork->user->name,
                        'avatar_path' => $artwork->user->avatar_path
                    ],
                    'created_at' => $artwork->created_at
                ];
            })
        ]);
    }
}
