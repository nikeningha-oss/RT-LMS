<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WithdrawalRequest extends Model
{
    use HasFactory;

    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_COMPLETED = 'completed';
    const STATUS_REJECTED = 'rejected';

    const FEE_RATE = 5; // 5%

    const PAYMENT_MTN = 'mtn';
    const PAYMENT_ORANGE = 'orange';
    const PAYMENT_BANK = 'bank';

    protected $fillable = [
        'driver_id',
        'amount',
        'fee',
        'net_amount',
        'payment_method',
        'account_details',
        'status',
        'admin_id',
        'admin_note',
        'requested_at',
        'processed_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'fee' => 'decimal:2',
        'net_amount' => 'decimal:2',
        'requested_at' => 'datetime',
        'processed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ================================
    // RELATIONSHIPS
    // ================================

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    // ================================
    // ACCESSORS
    // ================================

    public function getFormattedAmountAttribute()
    {
        return number_format($this->amount, 0, ',', ' ') . ' F';
    }

    public function getFormattedFeeAttribute()
    {
        return number_format($this->fee, 0, ',', ' ') . ' F';
    }

    public function getFormattedNetAmountAttribute()
    {
        return number_format($this->net_amount, 0, ',', ' ') . ' F';
    }

    public function getPaymentMethodLabelAttribute()
    {
        return [
            self::PAYMENT_MTN => 'MTN Mobile Money',
            self::PAYMENT_ORANGE => 'Orange Money',
            self::PAYMENT_BANK => 'Bank Transfer',
        ][$this->payment_method] ?? $this->payment_method;
    }

    public function getStatusLabelAttribute()
    {
        return [
            self::STATUS_PENDING => '⏳ Pending',
            self::STATUS_APPROVED => '✅ Approved',
            self::STATUS_COMPLETED => '💰 Completed',
            self::STATUS_REJECTED => '❌ Rejected',
        ][$this->status] ?? $this->status;
    }

    public function getStatusBadgeAttribute()
    {
        return [
            self::STATUS_PENDING => 'badge-pending',
            self::STATUS_APPROVED => 'badge-approved',
            self::STATUS_COMPLETED => 'badge-completed',
            self::STATUS_REJECTED => 'badge-rejected',
        ][$this->status] ?? 'badge-pending';
    }

    // ================================
    // HELPERS
    // ================================

    public static function calculateFee($amount)
    {
        return round($amount * (self::FEE_RATE / 100), 2);
    }

    public static function calculateNetAmount($amount)
    {
        $fee = self::calculateFee($amount);
        return $amount - $fee;
    }

    public function isPending()
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isApproved()
    {
        return $this->status === self::STATUS_APPROVED;
    }

    public function isCompleted()
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    public function isRejected()
    {
        return $this->status === self::STATUS_REJECTED;
    }

    // ================================
    // BUSINESS LOGIC
    // ================================

    /**
     * ✅ FIXED: Approve the withdrawal request
     * Balance was already deducted when request was created
     * Only update total_withdrawn
     */
    public function approve($adminId, $note = null)
    {
        $this->status = self::STATUS_APPROVED;
        $this->admin_id = $adminId;
        $this->admin_note = $note;
        $this->processed_at = now();
        $this->save();

        // ✅ Only update total_withdrawn (balance already deducted in store)
        $driver = $this->driver;
        if ($driver) {
            $driver->total_withdrawn += $this->amount;
            $driver->save();
        }

        return $this;
    }

    /**
     * ✅ FIXED: Complete the withdrawal (payment sent)
     * Sets processed_at so it counts for "Completed This Month"
     */
    public function complete($adminId, $note = null)
    {
        $this->status = self::STATUS_COMPLETED;
        $this->admin_id = $adminId;
        if ($note) {
            $this->admin_note = $note;
        }
        // ✅ IMPORTANT: Set processed_at so it counts for "Completed This Month"
        $this->processed_at = now();
        $this->save();

        Log::info('Withdrawal marked as completed', [
            'withdrawal_id' => $this->id,
            'processed_at' => $this->processed_at,
            'status' => $this->status
        ]);

        return $this;
    }

    /**
     * ✅ FIXED: Reject the withdrawal request
     * Refund the amount back to driver's balance
     */
    public function reject($adminId, $reason = null)
    {
        $this->status = self::STATUS_REJECTED;
        $this->admin_id = $adminId;
        $this->admin_note = $reason;
        $this->processed_at = now();
        $this->save();

        // ✅ REFUND the amount back to driver's available balance
        $driver = $this->driver;
        if ($driver) {
            $driver->available_balance += $this->amount;
            $driver->total_withdrawn -= $this->amount;
            $driver->save();

            Log::info('Withdrawal rejected - Amount refunded', [
                'driver_id' => $driver->id,
                'amount' => $this->amount,
                'new_balance' => $driver->available_balance,
                'total_withdrawn' => $driver->total_withdrawn
            ]);
        }

        return $this;
    }
}