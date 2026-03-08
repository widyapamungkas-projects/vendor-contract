<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TransportRate extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'transport_vehicle_id',
        'route_name',
        'route_type',
        'duration',
        'price',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    public function vehicle()
    {
        return $this->belongsTo(TransportVehicle::class, 'transport_vehicle_id');
    }
}