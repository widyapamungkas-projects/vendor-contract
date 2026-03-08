<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ActivityItem extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'activity_contract_id', 'activity_type', 'activity_name',
        'duration', 'min_pax', 'notes',
    ];

    public function contract()
    {
        return $this->belongsTo(ActivityContract::class, 'activity_contract_id');
    }

    public function rates()
    {
        return $this->hasMany(ActivityRate::class);
    }
}