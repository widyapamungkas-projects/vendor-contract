<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class GuideServiceTier extends Model
{
    use HasUuids;

    protected $fillable = ['guide_service_id', 'min_pax', 'max_pax', 'rate'];

    public function service()
    {
        return $this->belongsTo(GuideService::class, 'guide_service_id');
    }
}
