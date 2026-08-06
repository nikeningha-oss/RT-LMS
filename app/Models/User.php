<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'role',
        'approval_status',
        'approved_at',
        'approved_by',
        'rejection_reason',
        'is_available',
        'license_number',  // ✅ ADDED - Store license number at user level too
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'approved_at' => 'datetime',
        'is_available' => 'boolean',
    ];

    // ================================
    // ROLE CHECK METHODS
    // ================================

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isDriver(): bool
    {
        return $this->role === 'driver';
    }

    public function isCustomer(): bool
    {
        return $this->role === 'customer';
    }

    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    public static function getRoles(): array
    {
        return ['admin', 'driver', 'customer'];
    }

    // ================================
    // APPROVAL METHODS
    // ================================

    public function isPending(): bool
    {
        return $this->approval_status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->approval_status === 'approved' || $this->approval_status === 'active';
    }

    public function isRejected(): bool
    {
        return $this->approval_status === 'rejected';
    }

    public function canAccessSystem(): bool
    {
        return $this->isApproved();
    }

    public function approve($adminId)
    {
        $this->update([
            'approval_status' => 'approved',
            'approved_at' => now(),
            'approved_by' => $adminId,
            'rejection_reason' => null,
        ]);
    }

    public function reject($adminId, $reason = null)
    {
        $this->update([
            'approval_status' => 'rejected',
            'approved_by' => $adminId,
            'rejection_reason' => $reason,
            'approved_at' => null,
        ]);
    }

    // ================================
    // RELATIONSHIPS
    // ================================

    public function driver()
    {
        return $this->hasOne(Driver::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'customer_id');
    }

    public function assignedOrders()
    {
        return $this->hasMany(Order::class, 'driver_id');
    }

    public function deliveries()
    {
        return $this->hasManyThrough(
            Delivery::class,
            Driver::class,
            'user_id',
            'driver_id',
            'id',
            'id'
        );
    }

    public function vehicle()
    {
        return $this->hasOneThrough(
            Vehicle::class,
            Driver::class,
            'user_id',
            'id',
            'id',
            'vehicle_id'
        );
    }

    public function locations()
    {
        return $this->hasManyThrough(
            Location::class,
            Driver::class,
            'user_id',
            'locatable_id',
            'id',
            'id'
        )->where('locatable_type', Driver::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function unreadNotifications()
    {
        return $this->notifications()->where('is_read', false);
    }

    public function packages()
    {
        return $this->hasManyThrough(
            Package::class,
            Order::class,
            'customer_id',
            'order_id',
            'id',
            'id'
        );
    }

    public function activeOrders()
    {
        return $this->orders()->whereNotIn('status', ['delivered', 'cancelled']);
    }

    public function completedOrders()
    {
        return $this->orders()->where('status', 'delivered');
    }

    // ================================
    // HELPER METHODS
    // ================================

    public function hasDriverProfile(): bool
    {
        return $this->driver()->exists();
    }

    public function getDisplayNameAttribute(): string
    {
        return $this->name . ' (' . ucfirst($this->role) . ')';
    }

    public function getRoleBadgeColorAttribute(): string
    {
        return match($this->role) {
            'admin' => 'danger',
            'driver' => 'success',
            'customer' => 'info',
            default => 'secondary'
        };
    }

    public function getRoleIconAttribute(): string
    {
        return match($this->role) {
            'admin' => 'ti ti-crown',
            'driver' => 'ti ti-truck',
            'customer' => 'ti ti-user',
            default => 'ti ti-user'
        };
    }

    public function canBeDriver(): bool
    {
        return $this->isDriver() && $this->hasDriverProfile();
    }

    public function getInitialsAttribute(): string
    {
        $words = explode(' ', $this->name);
        $initials = '';
        foreach ($words as $word) {
            if (!empty($word)) {
                $initials .= strtoupper($word[0]);
            }
        }
        return substr($initials, 0, 2);
    }

    public function getPhoneFormattedAttribute(): string
    {
        return $this->phone ?? 'N/A';
    }

    public function isAvailable(): bool
    {
        return $this->is_available ?? true;
    }

    // ================================
    // SCOPES
    // ================================

    public function scopeRole($query, string $role)
    {
        return $query->where('role', $role);
    }

    public function scopeAdmins($query)
    {
        return $query->where('role', 'admin');
    }

    public function scopeDrivers($query)
    {
        return $query->where('role', 'driver');
    }

    public function scopeCustomers($query)
    {
        return $query->where('role', 'customer');
    }

    public function scopeWithDriverProfile($query)
    {
        return $query->whereHas('driver');
    }

    public function scopeAvailableDrivers($query)
    {
        return $query->where('role', 'driver')
                     ->whereHas('driver', function ($q) {
                         $q->where('is_available', true);
                     });
    }

    public function scopePending($query)
    {
        return $query->where('approval_status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('approval_status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('approval_status', 'rejected');
    }

    public function scopeAvailable($query)
    {
        return $query->where('is_available', true);
    }
}