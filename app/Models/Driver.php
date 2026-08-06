<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Driver extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'license_number',
        'phone',
        // ❌ REMOVED 'name' - name belongs to User model
        'is_available',
        'current_lat',
        'current_lng',
        'current_speed',
        'last_known_location_at',
        'vehicle_id',
        'total_earned',
        'total_withdrawn',
        'available_balance',
        'verified_at',
        'last_payment_date',
        'next_payment_date',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_available' => 'boolean',
        'current_lat' => 'decimal:8',
        'current_lng' => 'decimal:8',
        'current_speed' => 'decimal:2',
        'last_known_location_at' => 'datetime',
        'total_earned' => 'decimal:2',
        'total_withdrawn' => 'decimal:2',
        'available_balance' => 'decimal:2',
        'verified_at' => 'datetime',
        'last_payment_date' => 'datetime',
        'next_payment_date' => 'datetime',
    ];

    // ================================
    // RELATIONSHIPS
    // ================================

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'driver_id', 'user_id');
    }

    public function locations()
    {
        return $this->morphMany(Location::class, 'locatable');
    }

    public function withdrawalRequests()
    {
        return $this->hasMany(WithdrawalRequest::class);
    }

    // ================================
    // ACCESSORS - FIXED
    // ================================

    // ✅ FIXED: Get name from user relationship
    public function getNameAttribute()
    {
        if ($this->user) {
            return $this->user->name;
        }
        return null;  // Don't try to access a 'name' column in drivers table
    }

    // ✅ FIXED: Get phone from driver or user
    public function getPhoneNumberAttribute()
    {
        if (!empty($this->attributes['phone'])) {
            return $this->attributes['phone'];
        }
        if ($this->user && !empty($this->user->phone)) {
            return $this->user->phone;
        }
        return 'N/A';
    }

    public function getEmailAttribute()
    {
        if ($this->user) {
            return $this->user->email;
        }
        return null;
    }

    public function getFormattedTotalEarningsAttribute()
    {
        return number_format($this->total_earned ?? 0, 0, ',', ' ') . ' F';
    }

    public function getFormattedTotalWithdrawnAttribute()
    {
        return number_format($this->total_withdrawn ?? 0, 0, ',', ' ') . ' F';
    }

    public function getFormattedAvailableBalanceAttribute()
    {
        return number_format($this->available_balance ?? 0, 0, ',', ' ') . ' F';
    }

    // ================================
    // BUSINESS LOGIC
    // ================================

    public function isAvailable()
    {
        return $this->is_available && $this->user && $this->user->isApproved();
    }

    public function setAvailable()
    {
        $this->update(['is_available' => true]);
    }

    public function setUnavailable()
    {
        $this->update(['is_available' => false]);
    }

    public function updateLocation($lat, $lng, $speed = null)
    {
        $this->update([
            'current_lat' => $lat,
            'current_lng' => $lng,
            'current_speed' => $speed ?? $this->current_speed,
            'last_known_location_at' => now(),
        ]);
    }

    public function getActiveOrder()
    {
        return Order::where('driver_id', $this->user_id)
            ->whereNotIn('status', ['delivered', 'cancelled'])
            ->first();
    }

    public function calculateETA($destLat, $destLng)
    {
        if (!$this->current_lat || !$this->current_lng) {
            return null;
        }

        $distance = $this->calculateDistance(
            $this->current_lat,
            $this->current_lng,
            $destLat,
            $destLng
        );

        return max(1, round($distance / 25 * 60));
    }

    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $R = 6371;
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat/2) * sin($dLat/2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon/2) * sin($dLon/2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $R * $c;
    }

    // ================================
    // ✅ BALANCE METHODS
    // ================================

    public function addEarnings($amount)
    {
        $this->total_earned += $amount;
        $this->available_balance += $amount;
        $this->save();
        
        \Log::info('💰 Driver balance updated', [
            'driver_id' => $this->id,
            'amount_added' => $amount,
            'new_total_earned' => $this->total_earned,
            'new_available_balance' => $this->available_balance
        ]);
    }

    public function hasSufficientBalance($amount)
    {
        return $this->available_balance >= $amount;
    }

    public function getVerificationDate()
    {
        if ($this->verified_at) {
            return $this->verified_at;
        }
        
        if ($this->user && $this->user->approved_at) {
            return $this->user->approved_at;
        }
        
        return $this->created_at;
    }

    public function getNextPaymentDate()
    {
        if ($this->next_payment_date) {
            return $this->next_payment_date;
        }

        $verificationDate = $this->getVerificationDate();
        
        if (!$verificationDate) {
            return now()->addMonth();
        }

        $nextPayment = $verificationDate->copy()->addMonth();
        
        while ($nextPayment < now()) {
            $nextPayment->addMonth();
        }
        
        return $nextPayment;
    }

    public function isDueForPayment()
    {
        $nextPaymentDate = $this->getNextPaymentDate();
        return $nextPaymentDate <= now();
    }

    public function processMonthlyPayment()
    {
        $monthlyEarnings = Order::where('driver_id', $this->user_id)
            ->where('status', 'delivered')
            ->whereBetween('updated_at', [
                $this->last_payment_date ?? $this->getVerificationDate(),
                now()
            ])
            ->sum('driver_earning');
        
        if ($monthlyEarnings > 0) {
            $this->addEarnings($monthlyEarnings);
        }
        
        $this->last_payment_date = now();
        $this->next_payment_date = $this->getNextPaymentDate();
        $this->save();
        
        return $monthlyEarnings;
    }

    // ================================
    // SCOPES
    // ================================

    public function scopeAvailable($query)
    {
        return $query->where('is_available', true);
    }

    public function scopeWithActiveOrder($query)
    {
        return $query->whereHas('orders', function ($q) {
            $q->whereNotIn('status', ['delivered', 'cancelled']);
        });
    }

    public function scopeNearby($query, $lat, $lng, $radius = 10)
    {
        return $query->whereRaw(
            "(6371 * acos(cos(radians(?)) * cos(radians(current_lat)) * cos(radians(current_lng) - radians(?)) + sin(radians(?)) * sin(radians(current_lat)))) < ?",
            [$lat, $lng, $lat, $radius]
        );
    }

    public function scopeDueForPayment($query)
    {
        return $query->whereNotNull('verified_at')
            ->where('next_payment_date', '<=', now())
            ->where('is_available', true);
    }
}