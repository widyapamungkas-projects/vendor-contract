<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TransportVehicle extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'transport_contract_id',
        'category',
        'vehicle_name',
        'capacity',
        'brand',
        'notes',
    ];

    public function contract()
    {
        return $this->belongsTo(TransportContract::class, 'transport_contract_id');
    }

    public function rates()
    {
        return $this->hasMany(TransportRate::class);
    }
}