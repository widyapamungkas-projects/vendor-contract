<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class TourPackageRoomConfig extends Model
{
    use HasUuids;
    protected $fillable = ['tour_package_id','sgl','twn','trp','foc','margin_twn','margin_room','nights','with_tl'];
    public function package() { return $this->belongsTo(TourPackage::class, 'tour_package_id'); }
}
