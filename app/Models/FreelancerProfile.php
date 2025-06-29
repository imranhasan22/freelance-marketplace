<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FreelancerProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'hourly_rate',
        'skills',
        'portfolio',
        'availability',
        'experience_level',
        'languages',
        'education',
        'certifications',
    ];

    protected $casts = [
        'skills' => 'array',
        'portfolio' => 'array',
        'languages' => 'array',
        'education' => 'array',
        'certifications' => 'array',
        'hourly_rate' => 'decimal:2',
    ];

    // Experience levels
    const EXPERIENCE_BEGINNER = 'beginner';
    const EXPERIENCE_INTERMEDIATE = 'intermediate';
    const EXPERIENCE_EXPERT = 'expert';

    // Availability status
    const AVAILABILITY_AVAILABLE = 'available';
    const AVAILABILITY_BUSY = 'busy';
    const AVAILABILITY_UNAVAILABLE = 'unavailable';

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getAverageRatingAttribute()
    {
        return $this->user->receivedReviews()->avg('rating') ?? 0;
    }

    public function getTotalReviewsAttribute()
    {
        return $this->user->receivedReviews()->count();
    }
}
