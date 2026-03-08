<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EntranceRate extends Model {
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = ['entrance_ticket_id','pax_type','price'];
    protected $casts = ['price' => 'decimal:2'];

    public function ticket() { return $this->belongsTo(EntranceTicket::class, 'entrance_ticket_id'); }
}
