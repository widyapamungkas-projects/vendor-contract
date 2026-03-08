<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoomRate extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'hotel_contract_id',
        'room_type',
        'rate',
        'extra_bed_rate',
        'breakfast_rate',
        'notes',
    ];

    protected $casts = [
        'rate'           => 'decimal:2',
        'extra_bed_rate' => 'decimal:2',
        'breakfast_rate' => 'decimal:2',
    ];

    public function hotelContract(): BelongsTo
    {
        return $this->belongsTo(HotelContract::class);
    }
}
