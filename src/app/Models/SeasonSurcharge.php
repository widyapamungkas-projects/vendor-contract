<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SeasonSurcharge extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'hotel_contract_id',
        'season_name',
        'surcharge_amount',
        'surcharge_type',
        'start_date',
        'end_date',
        'notes',
    ];

    protected $casts = [
        'surcharge_amount' => 'decimal:2',
        'start_date'       => 'date',
        'end_date'         => 'date',
    ];

    public function hotelContract(): BelongsTo
    {
        return $this->belongsTo(HotelContract::class);
    }
}
