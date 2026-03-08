<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class PaymentSlipNotification extends Model
{
    use HasUuids;

    protected $fillable = [
        'file_id', 'file_name', 'folder_id',
        'file_size', 'mime_type', 'uploaded_at', 'is_read'
    ];

    protected $casts = [
        'uploaded_at' => 'datetime',
        'is_read'     => 'boolean',
    ];
}
