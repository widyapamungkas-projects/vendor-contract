<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;

class GuideLanguage extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = ['language_name', 'destination', 'currency', 'notes'];

    public function services()
    {
        return $this->hasMany(GuideService::class);
    }
}
