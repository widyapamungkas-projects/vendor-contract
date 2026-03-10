<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class TourPackageFixedCost extends Model
{
    use HasUuids;
    protected $fillable = ['tour_package_id','cost_type','label','amount','meta','sort_order'];
    protected $casts = ['meta' => 'array', 'amount' => 'decimal:2'];

    public function package() { return $this->belongsTo(TourPackage::class, 'tour_package_id'); }
}
