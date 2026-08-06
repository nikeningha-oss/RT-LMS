<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Earning extends Model
{
    use HasFactory;

    protected $fillable = [
        'driver_id',
        'order_id',
        'amount',
        'type',
        'status',
        'description',
        'earned_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'earned_at' => 'datetime',
    ];

    // ================================
    // RELATIONSHIPS
    // ================================

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // ================================
    // STATUS CONSTANTS
    // ================================

    const STATUS_PENDING = 'pending';
    const STATUS_COMPLETED = 'completed';
    const STATUS_FAILED = 'failed';

    const TYPE_DELIVERY = 'delivery';
    const TYPE_BONUS = 'bonus';
    const TYPE_REFUND = 'refund';

    // ================================
    // ACCESSORS
    // ================================

    public function getFormattedAmountAttribute()
    {
        return number_format($this->amount, 0, ',', ' ') . ' F';
    }

    public function getStatusLabelAttribute()
    {
        return [
            self::STATUS_PENDING => '⏳ Pending',
            self::STATUS_COMPLETED => '✅ Completed',
            self::STATUS_FAILED => '❌ Failed',
        ][$this->status] ?? $this->status;
    }

    public function getTypeLabelAttribute()
    {
        return [
            self::TYPE_DELIVERY => '🚚 Delivery',
            self::TYPE_BONUS => '🎁 Bonus',
            self::TYPE_REFUND => '💰 Refund',
        ][$this->type] ?? $this->type;
    }

    // ================================
    // SCOPES
    // ================================

    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeForDriver($query, $driverId)
    {
        return $query->where('driver_id', $driverId);
    }

    public function scopeForDate($query, $date)
    {
        return $query->whereDate('earned_at', $date);
    }

    public function scopeForMonth($query, $month, $year = null)
    {
        $year = $year ?? now()->year;
        return $query->whereMonth('earned_at', $month)->whereYear('earned_at', $year);
    }
}