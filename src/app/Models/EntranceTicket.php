<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EntranceTicket extends Model {
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'attraction_type','attraction_name','currency',
        'adult_price','child_price','infant_price','notes',
    ];

    protected $casts = [
        'adult_price'  => 'decimal:2',
        'child_price'  => 'decimal:2',
        'infant_price' => 'decimal:2',
    ];
}
