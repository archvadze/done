<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Evaluation extends Model
{
    use HasFactory;

    protected $fillable = [
        'artwork_id',
        'evaluator_id',
        'score_technique',
        'score_composition',
        'score_originality',
        'score_impact',
        'overall_score',
        'feedback_text',
        'source',
        'metadata',
        'status',
        'moderation_notes',
    ];

    protected $casts = [
        'metadata' => 'array',
        'overall_score' => 'decimal:2',
        'score_technique' => 'integer',
        'score_composition' => 'integer',
        'score_originality' => 'integer',
        'score_impact' => 'integer',
    ];

    /**
     * Relationships
     */
    public function artwork(): BelongsTo
    {
        return $this->belongsTo(Artwork::class);
    }

    public function evaluator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'evaluator_id');
    }

    /**
     * Computed properties
     */
    public function getAverageScoreAttribute(): float
    {
        $scores = array_filter([
            $this->score_technique,
            $this->score_composition,
            $this->score_originality,
            $this->score_impact,
        ]);

        return count($scores) > 0 ? round(array_sum($scores) / count($scores), 2) : 0;
    }

    /**
     * Scopes
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeByHuman($query)
    {
        return $query->where('source', 'human');
    }

    public function scopeByAI($query)
    {
        return $query->where('source', 'ai');
    }

    /**
     * Mutators
     */
    public function setOverallScoreAttribute($value)
    {
        // Auto-calculate if not provided
        if (is_null($value)) {
            $this->attributes['overall_score'] = $this->getAverageScoreAttribute();
        } else {
            $this->attributes['overall_score'] = $value;
        }
    }

    /**
     * Validation rules
     */
    public static function validationRules(): array
    {
        return [
            'artwork_id' => 'required|exists:artworks,id',
            'evaluator_id' => 'nullable|exists:users,id',
            'score_technique' => 'nullable|integer|min:1|max:10',
            'score_composition' => 'nullable|integer|min:1|max:10',
            'score_originality' => 'nullable|integer|min:1|max:10',
            'score_impact' => 'nullable|integer|min:1|max:10',
            'feedback_text' => 'nullable|string|max:2000',
            'source' => 'required|in:human,ai,aggregate',
        ];
    }
}
