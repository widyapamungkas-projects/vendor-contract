<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class TourPackageHotel extends Model
{
    use HasUuids;
    protected $fillable = ['tour_package_id','hotel_contract_id','hotel_name','room_type','room_rate','nights','sort_order','meta','surcharge_nights','surcharge_rate'];
    public function package() { return $this->belongsTo(TourPackage::class, 'tour_package_id'); }
    public function hotelContract() { return $this->belongsTo(HotelContract::class, 'hotel_contract_id'); }
}
