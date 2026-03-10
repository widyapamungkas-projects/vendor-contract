<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class TourPackageItinerary extends Model
{
    use HasUuids;
    protected $table = 'tour_package_itinerary';
    protected $fillable = ['tour_package_id','day','item_name','item_type','ref_id','price_per_pax','sort_order'];
    public function package() { return $this->belongsTo(TourPackage::class, 'tour_package_id'); }
}
