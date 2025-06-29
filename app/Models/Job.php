<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'category_id',
        'budget_type',
        'budget_min',
        'budget_max',
        'fixed_budget',
        'deadline',
        'skills_required',
        'experience_level',
        'status',
        'featured',
    ];

    protected $casts = [
        'skills_required' => 'array',
        'deadline' => 'datetime',
        'budget_min' => 'decimal:2',
        'budget_max' => 'decimal:2',
        'fixed_budget' => 'decimal:2',
        'featured' => 'boolean',
    ];

    // Budget types
    const BUDGET_HOURLY = 'hourly';
    const BUDGET_FIXED = 'fixed';

    // Job status
    const STATUS_OPEN = 'open';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function proposals()
    {
        return $this->hasMany(Proposal::class);
    }

    public function acceptedProposal()
    {
        return $this->hasOne(Proposal::class)->where('status', Proposal::STATUS_ACCEPTED);
    }

    public function scopeOpen($query)
    {
        return $query->where('status', self::STATUS_OPEN);
    }

    public function scopeFeatured($query)
    {
        return $query->where('featured', true);
    }
}
