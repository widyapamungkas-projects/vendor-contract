<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;

class TourPackage extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'package_code','agent','destination','duration',
        'period_from','period_to','pax','actual_pax','margin','currency','mw_bottles_per_day','mw_price_per_dus','fg_garland_price','fg_flower_girl_price','mw_bottles_per_day','mw_price_per_dus','fg_garland_price','fg_flower_girl_price',
        'rate_usd','rate_myr','rate_sgd','rate_eur',
        'notes',
        'prop_hotel_table', 'prop_inclusion','prop_exclusion','prop_tnc','prop_itinerary','prop_menu',
        'prop_custom_tables','prop_itin_briefs','created_by',
    ];

    protected $casts = [
        'period_from'        => 'date',
        'period_to'          => 'date',
        'prop_custom_tables' => 'array',
        'prop_itin_briefs'   => 'array',
    ];

    public function fixedCosts() { return $this->hasMany(TourPackageFixedCost::class, 'tour_package_id')->orderBy('sort_order'); }
    public function laCosts()    { return $this->hasMany(TourPackageLaCost::class)->orderBy('sort_order'); }
    public function itinerary()  { return $this->hasMany(TourPackageItinerary::class)->orderBy('day')->orderBy('sort_order'); }
    public function hotels()     { return $this->hasMany(TourPackageHotel::class)->orderBy('sort_order'); }
    public function roomConfig() { return $this->hasOne(TourPackageRoomConfig::class); }

    public function getConversionRate(): float
    {
        return match($this->currency) {
            'USD' => (float) $this->rate_usd,
            'MYR' => (float) $this->rate_myr,
            'SGD' => (float) $this->rate_sgd,
            'EUR' => (float) $this->rate_eur,
            default => 1,
        };
    }

    public static function generateCode(): string
    {
        $prefix = 'PKG-' . date('Ym') . '-';
        $last = static::where('package_code', 'like', $prefix . '%')->orderByDesc('package_code')->first();
        $num = $last ? (intval(substr($last->package_code, -4)) + 1) : 1;
        return $prefix . str_pad($num, 4, '0', STR_PAD_LEFT);
    }
}
