<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;

class GuideService extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = ['guide_language_id', 'service_name', 'service_type', 'unit_label', 'notes'];

    public function language()
    {
        return $this->belongsTo(GuideLanguage::class, 'guide_language_id');
    }

    public function tiers()
    {
        return $this->hasMany(GuideServiceTier::class)->orderBy('min_pax');
    }

    public function getRateForPax(int $pax): float
    {
        $tier = $this->tiers()
            ->where('min_pax', '<=', $pax)
            ->where(function($q) use ($pax) {
                $q->whereNull('max_pax')->orWhere('max_pax', '>=', $pax);
            })
            ->orderByDesc('min_pax')
            ->first();

        return $tier ? (float) $tier->rate : 0;
    }

    public static function serviceTypeLabel(string $type): string
    {
        return match($type) {
            'airport_transfer' => 'Airport Transfer',
            'full_day'         => 'Full Day',
            'half_day'         => 'Half Day',
            'overtime'         => 'Overtime (per jam)',
            'tipping'          => 'Tipping (per hari)',
            'package'          => 'Paket',
            default            => $type,
        };
    }
}
