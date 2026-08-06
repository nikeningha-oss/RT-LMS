<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vehicle extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'driver_id',        // This matches your Driver model's vehicle_id
        'plate_number',
        'model',
        'color',
        'year',
        'capacity',
        'type',
        'is_active',
        'status',
        'make',
        'vin',
        'mileage',
        'last_service_date',
        'insurance_expiry',
        'notes',
        'current_latitude',
        'current_longitude',
        'last_known_location_at',
        'registration_number',
        'fuel_type',
        'fuel_consumption',
        'gps_device_id',
        'tracking_number',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'capacity' => 'decimal:2',
        'year' => 'integer',
        'mileage' => 'integer',
        'last_service_date' => 'date',
        'insurance_expiry' => 'date',
        'current_latitude' => 'decimal:8',
        'current_longitude' => 'decimal:8',
        'last_known_location_at' => 'datetime',
        'fuel_consumption' => 'decimal:2',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the driver assigned to this vehicle.
     * This matches your Driver model's vehicle() relationship
     */
    public function driver()
    {
        return $this->belongsTo(Driver::class, 'driver_id');
    }

    /**
     * Get the deliveries for this vehicle.
     */
    public function deliveries()
    {
        return $this->hasMany(Delivery::class);
    }

    /**
     * Get the orders for this vehicle (if you use Order model).
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Get the location history for this vehicle.
     */
    public function locations()
    {
        return $this->morphMany(Location::class, 'locatable');
    }

    /**
     * Scope for available vehicles.
     */
    public function scopeAvailable($query)
    {
        return $query->where('status', 'available')
                     ->where('is_active', true);
    }

    /**
     * Scope for online vehicles.
     */
    public function scopeOnline($query)
    {
        return $query->whereIn('status', ['available', 'on_delivery', 'idle'])
                     ->where('is_active', true);
    }

    /**
     * Scope for vehicles with drivers assigned.
     */
    public function scopeWithDrivers($query)
    {
        return $query->whereNotNull('driver_id');
    }

    /**
     * Scope for active vehicles.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for vehicles by type.
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Check if vehicle is online/active.
     */
    public function isOnline()
    {
        return $this->is_active && in_array($this->status, ['available', 'on_delivery', 'idle']);
    }

    /**
     * Get status color for dashboard.
     */
    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'available' => '#10B981', // Green
            'on_delivery' => '#F59E0B', // Amber
            'idle' => '#FCD34D', // Yellow
            'maintenance' => '#EF4444', // Red
            'offline' => '#9CA3AF', // Gray
            default => '#6B7280'
        };
    }

    /**
     * Get status badge class for UI.
     */
    public function getStatusBadgeClassAttribute()
    {
        return match($this->status) {
            'available' => 'bg-green-100 text-green-800',
            'on_delivery' => 'bg-yellow-100 text-yellow-800',
            'idle' => 'bg-blue-100 text-blue-800',
            'maintenance' => 'bg-red-100 text-red-800',
            'offline' => 'bg-gray-100 text-gray-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    /**
     * Get full vehicle name.
     */
    public function getFullNameAttribute()
    {
        return $this->make 
            ? "{$this->make} {$this->model} ({$this->plate_number})"
            : "{$this->model} ({$this->plate_number})";
    }

    /**
     * Get driver name if assigned.
     */
    public function getDriverNameAttribute()
    {
        return $this->driver ? $this->driver->user->name ?? 'Unknown' : 'Unassigned';
    }

    /**
     * Check if vehicle has driver assigned.
     */
    public function hasDriver()
    {
        return !is_null($this->driver_id);
    }

    /**
     * Assign driver to vehicle.
     */
    public function assignDriver($driverId)
    {
        // Update vehicle
        $this->update(['driver_id' => $driverId]);
        
        // Update driver's vehicle_id (matches your Driver model)
        if ($driver = Driver::find($driverId)) {
            $driver->update(['vehicle_id' => $this->id]);
        }
        
        return $this;
    }

    /**
     * Unassign driver from vehicle.
     */
    public function unassignDriver()
    {
        // Remove vehicle from driver
        if ($this->driver) {
            $this->driver->update(['vehicle_id' => null]);
        }
        
        // Remove driver from vehicle
        $this->update(['driver_id' => null]);
        
        return $this;
    }

    /**
     * Update vehicle location (GPS tracking).
     */
    public function updateLocation($latitude, $longitude)
    {
        $this->update([
            'current_latitude' => $latitude,
            'current_longitude' => $longitude,
            'last_known_location_at' => now(),
        ]);

        // Optionally save location history
        // if you have a Location model
        // $this->locations()->create([
        //     'latitude' => $latitude,
        //     'longitude' => $longitude,
        //     'recorded_at' => now(),
        // ]);

        return $this;
    }

    /**
     * Check if vehicle needs maintenance/service.
     */
    public function needsService()
    {
        if (!$this->last_service_date) {
            return true;
        }
        
        return $this->last_service_date->addMonths(6)->isPast();
    }

    /**
     * Check if insurance is expired.
     */
    public function isInsuranceExpired()
    {
        if (!$this->insurance_expiry) {
            return true;
        }
        
        return $this->insurance_expiry->isPast();
    }

    /**
     * Get vehicles with their driver status for dashboard.
     */
    public static function getDashboardVehicles()
    {
        return self::with('driver.user')
                   ->where('is_active', true)
                   ->orderBy('status')
                   ->get()
                   ->map(function ($vehicle) {
                       return [
                           'id' => $vehicle->id,
                           'plate_number' => $vehicle->plate_number,
                           'model' => $vehicle->model,
                           'type' => $vehicle->type,
                           'status' => $vehicle->status,
                           'status_color' => $vehicle->status_color,
                           'driver' => $vehicle->driver_name,
                           'driver_available' => $vehicle->driver ? $vehicle->driver->is_available : false,
                           'current_lat' => $vehicle->current_latitude,
                           'current_lng' => $vehicle->current_longitude,
                           'last_known_location_at' => $vehicle->last_known_location_at,
                       ];
                   });
    }
}