<?php

namespace App\Models;

use App\Models\Document;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vehicle extends Model
{
    protected $fillable =
    [
        'placa',
        'modelo',
        'marca',
        'year',
        'status',
    ];
    public function documents(): HasMany
    {
        return $this->hasMany(
            related: Document::class,
            foreignKey: 'vehicle_id',
        );
    }
}
