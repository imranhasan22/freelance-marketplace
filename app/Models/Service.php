<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'category_id',
        'title',
        'description',
        'price',
        'delivery_time',
        'revisions',
        'features',
        'images',
        'status',
        'featured',
    ];

    protected $casts = [
        'features' => 'array',
        'images' => 'array',
        'price' => 'decimal:2',
        'featured' => 'boolean',
    ];

    // Service status
    const STATUS_ACTIVE = 'active';
    const STATUS_PAUSED = 'paused';
    const STATUS_DRAFT = 'draft';

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function getAverageRatingAttribute()
    {
        return $this->reviews()->avg('rating') ?? 0;
    }

    public function getTotalReviewsAttribute()
    {
        return $this->reviews()->count();
    }

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopeFeatured($query)
    {
        return $query->where('featured', true);
    }
}
