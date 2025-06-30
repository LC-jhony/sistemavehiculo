<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DriverLicense extends Model
{
    protected $fillable = [
        'driver_id',
        'license_number',
        'expiration_date',
        'license_type',
        'file',
    ];

    protected $casts = [
        'file' => 'array',
        'expiration_date' => 'date',
    ];

    public function driver(): BelongsTo
    {
        return $this->belongsTo(
            related: Driver::class,
            foreignKey: 'driver_id',
        );
    }
}
