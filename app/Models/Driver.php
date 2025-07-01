<?php

namespace App\Models;

use App\Observers\DriverObserver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;

#[ObservedBy(DriverObserver::class)]
class Driver extends Model
{
    use Notifiable;
    protected $fillable = [
        'name',
        'last_paternal_name',
        'last_maternal_name',
        'dni',
        'cargo_id',
        'file',
        'status',
    ];

    public function getFullNameAttribute(): string
    {
        return trim("{$this->name} {$this->last_paternal_name} {$this->last_maternal_name}");
    }

    public function cargo(): BelongsTo
    {
        return $this->belongsTo(
            related: Cargo::class,
            foreignKey: 'cargo_id',
        );
    }

    public function licenses(): HasMany
    {
        return $this->hasMany(
            related: DriverLicense::class,
            foreignKey: 'driver_id',
        );
    }
}
