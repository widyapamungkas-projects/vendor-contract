<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class TourPackageLaCost extends Model
{
    use HasUuids;
    protected $fillable = ['tour_package_id','name','amount','sort_order'];
    public function package() { return $this->belongsTo(TourPackage::class, 'tour_package_id'); }
}
