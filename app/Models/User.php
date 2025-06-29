<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'user_type',
        'avatar',
        'phone',
        'location',
        'is_verified',
        'two_factor_enabled',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_verified' => 'boolean',
        'two_factor_enabled' => 'boolean',
    ];

    // User types
    const TYPE_CLIENT = 'client';
    const TYPE_FREELANCER = 'freelancer';
    const TYPE_ADMIN = 'admin';

    public function isFreelancer()
    {
        return $this->user_type === self::TYPE_FREELANCER;
    }

    public function isClient()
    {
        return $this->user_type === self::TYPE_CLIENT;
    }

    public function isAdmin()
    {
        return $this->user_type === self::TYPE_ADMIN;
    }

    // Relationships
    public function freelancerProfile()
    {
        return $this->hasOne(FreelancerProfile::class);
    }

    public function clientProfile()
    {
        return $this->hasOne(ClientProfile::class);
    }

    public function jobs()
    {
        return $this->hasMany(Job::class);
    }

    public function services()
    {
        return $this->hasMany(Service::class);
    }

    public function proposals()
    {
        return $this->hasMany(Proposal::class);
    }

    public function givenReviews()
    {
        return $this->hasMany(Review::class, 'reviewer_id');
    }

    public function receivedReviews()
    {
        return $this->hasMany(Review::class, 'reviewee_id');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }
}
