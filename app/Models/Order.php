<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'customer_id',
        'driver_id',
        'vehicle_id',
        'status',
        'payment_status',
        'pickup_address',
        'delivery_address',
        'pickup_lat',
        'pickup_lng',
        'delivery_lat',
        'delivery_lng',
        'distance_km',
        'weight_kg',
        'total_price',
        'base_fare',
        'distance_charge',
        'weight_charge',
        'service_fee',
        'tax_rate',
        'tax_amount',
        'driver_earning',
        'driver_commission_rate',
        'platform_fee',
        'estimated_delivery',
        'actual_delivery',
        'notes',
    ];

    protected $casts = [
        'pickup_lat' => 'float',
        'pickup_lng' => 'float',
        'delivery_lat' => 'float',
        'delivery_lng' => 'float',
        'distance_km' => 'float',
        'weight_kg' => 'float',
        'total_price' => 'decimal:2',
        'base_fare' => 'decimal:2',
        'distance_charge' => 'decimal:2',
        'weight_charge' => 'decimal:2',
        'service_fee' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'driver_earning' => 'decimal:2',
        'driver_commission_rate' => 'decimal:2',
        'platform_fee' => 'decimal:2',
        'estimated_delivery' => 'datetime',
        'actual_delivery' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ================================
    // PRICING CONSTANTS (50/50 SPLIT)
    // ================================
    
    const BASE_FARE = 500;
    const PER_KM_RATE = 300;
    const PER_KG_RATE = 200;
    const TAX_RATE = 5;
    const DRIVER_COMMISSION = 50;
    const PLATFORM_COMMISSION = 50;
    const SERVICE_FEE_RATE = 5;

    // ================================
    // STATUS CONSTANTS
    // ================================

    const STATUS_PENDING = 'pending';
    const STATUS_PRICE_PENDING = 'price_pending';
    const STATUS_PRICE_CONFIRMED = 'price_confirmed';
    const STATUS_ASSIGNED = 'assigned';
    const STATUS_PICKED_UP = 'picked_up';
    const STATUS_IN_TRANSIT = 'in_transit';
    const STATUS_DELIVERED = 'delivered';
    const STATUS_CANCELLED = 'cancelled';

    const PAYMENT_PENDING = 'pending';
    const PAYMENT_PAID = 'paid';
    const PAYMENT_FAILED = 'failed';

    // ================================
    // RELATIONSHIPS
    // ================================

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    public function driverProfile()
    {
        return $this->belongsTo(Driver::class, 'driver_id', 'user_id');
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id');
    }

    public function packages()
    {
        return $this->hasMany(Package::class);
    }

    // ================================
    // ORDER NUMBER GENERATION
    // ================================

    public static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            if (!$order->order_number) {
                $order->order_number = self::generateOrderNumber();
            }
        });
    }

    public static function generateOrderNumber(): string
    {
        return 'ORD-' . now()->format('Ymd') . '-' . strtoupper(Str::random(5));
    }

    // ================================
    // ✅ PRICING CALCULATIONS
    // ================================

    /**
     * ✅ Calculate full price breakdown
     * Returns: base_fare, distance_charge, weight_charge, subtotal,
     *          service_fee, tax_rate, tax_amount, total_price,
     *          driver_earning (50% of subtotal), platform_fee (50% of subtotal)
     */
    public function calculatePrice(): array
    {
        $distance = $this->distance_km ?? 0;
        $weight = $this->weight_kg ?? 0;
        
        $baseFare = self::BASE_FARE;
        $distanceCharge = $distance * self::PER_KM_RATE;
        $weightCharge = $weight * self::PER_KG_RATE;
        $subtotal = $baseFare + $distanceCharge + $weightCharge;
        
        $serviceFee = $subtotal * (self::SERVICE_FEE_RATE / 100);
        $taxRate = self::TAX_RATE;
        $taxAmount = ($subtotal + $serviceFee) * ($taxRate / 100);
        $totalPrice = $subtotal + $serviceFee + $taxAmount;
        
        // ✅ 50/50 split on SUBTOTAL only
        $driverEarning = $subtotal * (self::DRIVER_COMMISSION / 100);
        $platformFee = $subtotal * (self::PLATFORM_COMMISSION / 100);
        
        return [
            'base_fare' => $baseFare,
            'distance_charge' => $distanceCharge,
            'weight_charge' => $weightCharge,
            'subtotal' => $subtotal,
            'service_fee' => $serviceFee,
            'tax_rate' => $taxRate,
            'tax_amount' => $taxAmount,
            'total_price' => $totalPrice,
            'driver_earning' => $driverEarning,
            'driver_commission_rate' => self::DRIVER_COMMISSION,
            'platform_fee' => $platformFee,
        ];
    }

    /**
     * ✅ Auto calculate price with minimum defaults
     */
    public function autoCalculatePrice(): array
    {
        $distance = $this->distance_km ?? 0;
        $weight = $this->weight_kg ?? 0;
        
        if ($distance == 0) $distance = 1;
        if ($weight == 0) $weight = 0.5;
        
        return $this->calculatePrice();
    }

    /**
     * ✅ Apply auto price to the order
     */
    public function applyAutoPrice(): void
    {
        $pricing = $this->autoCalculatePrice();
        
        $this->update([
            'total_price' => $pricing['total_price'],
            'base_fare' => $pricing['base_fare'],
            'distance_charge' => $pricing['distance_charge'],
            'weight_charge' => $pricing['weight_charge'],
            'service_fee' => $pricing['service_fee'],
            'tax_rate' => $pricing['tax_rate'],
            'tax_amount' => $pricing['tax_amount'],
            'driver_earning' => $pricing['driver_earning'],
            'driver_commission_rate' => $pricing['driver_commission_rate'],
            'platform_fee' => $pricing['platform_fee'],
            'status' => self::STATUS_PRICE_PENDING,
            'payment_status' => self::PAYMENT_PENDING,
        ]);
    }

    // ================================
    // ✅ SUBTOTAL ACCESSOR
    // ================================

    /**
     * ✅ Get the subtotal (base_fare + distance_charge + weight_charge)
     */
    public function getSubtotalAttribute(): float
    {
        return ($this->base_fare ?? 0) + 
               ($this->distance_charge ?? 0) + 
               ($this->weight_charge ?? 0);
    }

    // ================================
    // ✅ PRICE BREAKDOWN
    // ================================

    public function getPriceBreakdownAttribute(): array
    {
        return [
            'base_fare' => [
                'label' => 'Base Fare',
                'value' => $this->base_fare ?? 0,
                'formatted' => number_format($this->base_fare ?? 0, 0, ',', ' ') . ' F'
            ],
            'distance_charge' => [
                'label' => 'Distance (' . ($this->distance_km ?? 0) . ' km)',
                'value' => $this->distance_charge ?? 0,
                'formatted' => number_format($this->distance_charge ?? 0, 0, ',', ' ') . ' F'
            ],
            'weight_charge' => [
                'label' => 'Weight (' . ($this->weight_kg ?? 0) . ' kg)',
                'value' => $this->weight_charge ?? 0,
                'formatted' => number_format($this->weight_charge ?? 0, 0, ',', ' ') . ' F'
            ],
            'subtotal' => [
                'label' => 'Subtotal',
                'value' => $this->subtotal,
                'formatted' => number_format($this->subtotal, 0, ',', ' ') . ' F'
            ],
            'service_fee' => [
                'label' => 'Service Fee (5%)',
                'value' => $this->service_fee ?? 0,
                'formatted' => number_format($this->service_fee ?? 0, 0, ',', ' ') . ' F'
            ],
            'tax_amount' => [
                'label' => 'VAT (' . ($this->tax_rate ?? 0) . '%)',
                'value' => $this->tax_amount ?? 0,
                'formatted' => number_format($this->tax_amount ?? 0, 0, ',', ' ') . ' F'
            ],
            'driver_earning' => [
                'label' => '👨‍✈️ Driver Earns (50% of subtotal)',
                'value' => $this->driver_earning ?? 0,
                'formatted' => number_format($this->driver_earning ?? 0, 0, ',', ' ') . ' F'
            ],
            'platform_fee' => [
                'label' => '🏢 Platform Fee (50% of subtotal)',
                'value' => $this->platform_fee ?? 0,
                'formatted' => number_format($this->platform_fee ?? 0, 0, ',', ' ') . ' F'
            ],
            'total' => [
                'label' => '💰 Total',
                'value' => $this->total_price ?? 0,
                'formatted' => number_format($this->total_price ?? 0, 0, ',', ' ') . ' F'
            ]
        ];
    }

    public function getFormattedDriverEarningAttribute(): string
    {
        return number_format($this->driver_earning ?? 0, 0, ',', ' ') . ' F';
    }

    public function getDriverEarningRateAttribute(): string
    {
        return self::DRIVER_COMMISSION . '%';
    }

    public function getPlatformFeeRateAttribute(): string
    {
        return self::PLATFORM_COMMISSION . '%';
    }

    // ================================
    // STATUS HELPERS
    // ================================

    public static function getStatuses(): array
    {
        return [
            self::STATUS_PENDING => 'Pending',
            self::STATUS_PRICE_PENDING => 'Price Pending',
            self::STATUS_PRICE_CONFIRMED => 'Price Confirmed',
            self::STATUS_ASSIGNED => 'Assigned',
            self::STATUS_PICKED_UP => 'Picked Up',
            self::STATUS_IN_TRANSIT => 'In Transit',
            self::STATUS_DELIVERED => 'Delivered',
            self::STATUS_CANCELLED => 'Cancelled',
        ];
    }

    public static function getPaymentStatuses(): array
    {
        return [
            self::PAYMENT_PENDING => 'Pending',
            self::PAYMENT_PAID => 'Paid',
            self::PAYMENT_FAILED => 'Failed',
        ];
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isPricePending(): bool
    {
        return $this->status === self::STATUS_PRICE_PENDING;
    }

    public function isPriceConfirmed(): bool
    {
        return $this->status === self::STATUS_PRICE_CONFIRMED;
    }

    public function isDelivered(): bool
    {
        return $this->status === self::STATUS_DELIVERED;
    }

    public function isCancelled(): bool
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    public function isActive(): bool
    {
        return in_array($this->status, [
            self::STATUS_PENDING,
            self::STATUS_PRICE_PENDING,
            self::STATUS_PRICE_CONFIRMED,
            self::STATUS_ASSIGNED,
            self::STATUS_PICKED_UP,
            self::STATUS_IN_TRANSIT
        ]);
    }

    public function isPaid(): bool
    {
        return $this->payment_status === self::PAYMENT_PAID;
    }

    public function isPaymentPending(): bool
    {
        return $this->payment_status === self::PAYMENT_PENDING;
    }

    // ================================
    // ✅ BUSINESS LOGIC HELPERS
    // ================================

    public function canConfirmPrice(): bool
    {
        return $this->status === self::STATUS_PRICE_PENDING;
    }

    public function canPay(): bool
    {
        return $this->status === self::STATUS_PRICE_CONFIRMED && $this->isPaymentPending();
    }

    public function markAsDelivered(): void
    {
        $this->update([
            'status' => self::STATUS_DELIVERED,
            'actual_delivery' => now(),
        ]);
    }

    /**
     * ✅ FIXED: Mark order as paid - updates both payment and order status
     */
    public function markAsPaid(): void
    {
        $newStatus = $this->status;
        
        if ($this->status === self::STATUS_PRICE_CONFIRMED || 
            $this->status === self::STATUS_PRICE_PENDING) {
            $newStatus = self::STATUS_ASSIGNED;
        }
        
        $this->update([
            'payment_status' => self::PAYMENT_PAID,
            'status' => $newStatus,
        ]);
        
        Log::info('💰 Order marked as paid', [
            'order_id' => $this->id,
            'order_number' => $this->order_number,
            'old_status' => $this->getOriginal('status'),
            'new_status' => $newStatus,
            'payment_status' => self::PAYMENT_PAID
        ]);
    }

    public function confirmPrice(): void
    {
        $this->update([
            'status' => self::STATUS_PRICE_CONFIRMED,
        ]);
    }

    public function adminApprovePrice(): void
    {
        $this->update([
            'status' => self::STATUS_PRICE_CONFIRMED,
        ]);
    }

    public function setPrice($distance, $weight): void
    {
        $this->distance_km = $distance;
        $this->weight_kg = $weight;
        
        $pricing = $this->calculatePrice();
        
        $this->update([
            'total_price' => $pricing['total_price'],
            'base_fare' => $pricing['base_fare'],
            'distance_charge' => $pricing['distance_charge'],
            'weight_charge' => $pricing['weight_charge'],
            'service_fee' => $pricing['service_fee'],
            'tax_rate' => $pricing['tax_rate'],
            'tax_amount' => $pricing['tax_amount'],
            'driver_earning' => $pricing['driver_earning'],
            'driver_commission_rate' => $pricing['driver_commission_rate'],
            'platform_fee' => $pricing['platform_fee'],
            'status' => self::STATUS_PRICE_PENDING,
            'payment_status' => self::PAYMENT_PENDING,
        ]);
    }

    public function calculateDriverEarning(): float
    {
        if ($this->status !== self::STATUS_DELIVERED) {
            return 0;
        }
        return $this->driver_earning ?? 0;
    }
}