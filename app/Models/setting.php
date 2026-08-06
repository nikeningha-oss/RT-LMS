<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'description',
    ];

    // Default settings
    public static function getDefaults()
    {
        return [
            'driver_commission' => 50,    // 50% default
            'base_fare' => 500,
            'per_km_rate' => 300,
            'per_kg_rate' => 200,
            'tax_rate' => 5,
        ];
    }

    public static function getDriverCommission()
    {
        return self::where('key', 'driver_commission')->first()->value ?? 50;
    }

    public static function getPlatformCommission()
    {
        return 100 - self::getDriverCommission();
    }
}