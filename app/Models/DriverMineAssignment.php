<?php

namespace App\Models;

use Carbon\Carbon;
use App\Models\Mine;
use App\Models\Driver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DriverMineAssignment extends Model
{
    protected $fillable = [
        'driver_id',
        'mine_id',
        'start_date',
        'end_date',
        'month',
        'year',
        'status',
        'notes',
    ];
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'month' => 'integer',
        'year' => 'integer',
    ];

    // Relaciones
    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }

    public function mine(): BelongsTo
    {
        return $this->belongsTo(Mine::class);
    }

    // Scopes
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    public function scopeCurrentMonth(Builder $query): Builder
    {
        $now = Carbon::now();
        return $query->where('year', $now->year)
            ->where('month', $now->month);
    }

    public function scopeForPeriod(Builder $query, int $year, int $month): Builder
    {
        return $query->where('year', $year)->where('month', $month);
    }

    // Accessors
    public function getMonthNameAttribute(): string
    {
        $months = [
            1 => 'Enero',
            2 => 'Febrero',
            3 => 'Marzo',
            4 => 'Abril',
            5 => 'Mayo',
            6 => 'Junio',
            7 => 'Julio',
            8 => 'Agosto',
            9 => 'Septiembre',
            10 => 'Octubre',
            11 => 'Noviembre',
            12 => 'Diciembre'
        ];

        return $months[$this->month] ?? '';
    }

    public function getPeriodAttribute(): string
    {
        return $this->month_name . ' ' . $this->year;
    }

    public function getIsActiveAttribute(): bool
    {
        return $this->status === 'Activo' &&
            $this->start_date <= now() &&
            $this->end_date >= now();
    }
}
