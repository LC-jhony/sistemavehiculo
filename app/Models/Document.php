<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $fillable = [
        'vehicle_id',
        'type',
        'file',
    ];

    public function vehicle()
    {
        return $this->belongsTo(
            related: Vehicle::class,
            foreignKey: 'vehicle_id'
        );
    }
}
