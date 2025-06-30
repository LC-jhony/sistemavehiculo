<?php

namespace App\Models;

use App\Models\Driver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cargo extends Model
{
    protected $fillable = [
        'name',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    /**
     * Get the drivers for the cargo.
     */
    public function drivers(): HasMany
    {
        return $this->hasMany(
            related: Driver::class,
            foreignKey: 'cargo_id',
        );
    }
}
