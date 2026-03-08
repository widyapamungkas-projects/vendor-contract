<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RestaurantMenu extends Model {
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'restaurant_contract_id','menu_name','serving_style',
        'adult_price','child_price','min_pax','menu_details','notes',
    ];

    protected $casts = [
        'adult_price' => 'decimal:2',
        'child_price' => 'decimal:2',
    ];

    public function contract() {
        return $this->belongsTo(RestaurantContract::class, 'restaurant_contract_id');
    }

    public function getChildPriceAutoAttribute(): float {
        return round($this->adult_price * 0.65);
    }
}
