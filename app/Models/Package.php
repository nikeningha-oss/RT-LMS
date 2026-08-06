<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'order_id',
        'description',
        'weight',
        'dimensions',
        'quantity',
        'is_fragile',
        'tracking_number',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'weight' => 'decimal:2',
        'quantity' => 'integer',
        'is_fragile' => 'boolean',
    ];

    /**
     * Get the order that this package belongs to.
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the full description of the package.
     */
    public function getFullDescriptionAttribute()
    {
        return $this->description . ($this->quantity > 1 ? " (x{$this->quantity})" : "");
    }

    /**
     * Check if package is fragile.
     */
    public function isFragile()
    {
        return $this->is_fragile;
    }
}