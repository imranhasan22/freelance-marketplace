<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_id',
        'buyer_id',
        'seller_id',
        'amount',
        'status',
        'requirements',
        'delivery_date',
        'completed_at',
        'payment_status',
        'payment_id',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'delivery_date' => 'datetime',
        'completed_at' => 'datetime',
        'requirements' => 'array',
    ];

    // Order status
    const STATUS_PENDING = 'pending';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_DELIVERED = 'delivered';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_DISPUTED = 'disputed';

    // Payment status
    const PAYMENT_PENDING = 'pending';
    const PAYMENT_PAID = 'paid';
    const PAYMENT_RELEASED = 'released';
    const PAYMENT_REFUNDED = 'refunded';

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function deliverables()
    {
        return $this->hasMany(Deliverable::class);
    }
}
