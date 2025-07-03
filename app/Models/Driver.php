<?php

namespace App\Models;

use App\Observers\DriverObserver;
use App\Models\DriverMineAssignment;
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
    public function mine(): BelongsTo
    {
        return $this->belongsTo(
            related: Mine::class,
            foreignKey: 'mina_id',
        );
    }
    // Nuevas relaciones para minas
    public function mineAssignments(): HasMany
    {
        return $this->hasMany(DriverMineAssignment::class);
    }

    public function currentMineAssignment(): HasMany
    {
        return $this->hasMany(DriverMineAssignment::class)
            ->where('status', 'active')
            ->whereDate('start_date', '<=', now())
            ->whereDate('end_date', '>=', now());
    }

    public function getCurrentMineAttribute()
    {
        return $this->currentMineAssignment()->first()?->mine;
    }
}
