<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class HotelContract extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'contract_code',
        'hotel_name',
        
        
        'hotel_country', 'destination', 'area',
        'pic_name',
        'pic_phone',
        'pic_email',
        'contract_type',
        'price_category',
        'currency',
        'valid_from',
        'valid_until',
        'status',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'valid_from'  => 'date',
        'valid_until' => 'date',
    ];

    // -------------------------------------------------------
    // RELATIONSHIPS
    // -------------------------------------------------------

    public function roomRates(): HasMany
    {
        return $this->hasMany(RoomRate::class);
    }

    public function seasonSurcharges(): HasMany
    {
        return $this->hasMany(SeasonSurcharge::class);
    }

    public function blackoutDates(): HasMany
    {
        return $this->hasMany(BlackoutDate::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // -------------------------------------------------------
    // AUTO GENERATE CONTRACT CODE
    // Format: HC-YYYYMM-XXXX (contoh: HC-202603-0001)
    // -------------------------------------------------------

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($contract) {
            if (empty($contract->contract_code)) {
                $prefix = 'HC-' . now()->format('Ym') . '-';
                $last = static::where('contract_code', 'like', $prefix . '%')
                    ->orderByDesc('contract_code')
                    ->first();
                $number = $last
                    ? (int) substr($last->contract_code, -4) + 1
                    : 1;
                $contract->contract_code = $prefix . str_pad($number, 4, '0', STR_PAD_LEFT);
            }
        });
    }

    // -------------------------------------------------------
    // AUTO DETECT STATUS
    // -------------------------------------------------------

    public function updateStatus(): void
    {
        $today = Carbon::today();
        $expiringSoonThreshold = Carbon::today()->addDays(30);

        if ($this->valid_until < $today) {
            $this->status = 'expired';
        } elseif ($this->valid_until <= $expiringSoonThreshold) {
            $this->status = 'expiring_soon';
        } else {
            $this->status = 'active';
        }

        $this->saveQuietly();
    }

    // -------------------------------------------------------
    // SCOPES (filter query)
    // -------------------------------------------------------

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeExpired($query)
    {
        return $query->where('status', 'expired');
    }

    public function scopeExpiringSoon($query)
    {
        return $query->where('status', 'expiring_soon');
    }
}
