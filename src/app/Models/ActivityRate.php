<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ActivityRate extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'activity_item_id', 'rate_type', 'pax_type', 'min_pax', 'price',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    public function item()
    {
        return $this->belongsTo(ActivityItem::class, 'activity_item_id');
    }
}