<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

class ActivityContract extends Model {
    use HasFactory, HasUuids, SoftDeletes;
    protected $fillable = [
        'contract_code','vendor_name','vendor_city','vendor_country',
        'destination','area',
        'pic_name','pic_phone','pic_email','price_category','currency',
        'valid_from','valid_until','status','notes','created_by','updated_by',
    ];
    protected $casts = ['valid_from'=>'date','valid_until'=>'date'];
    protected static function booted(): void {
        static::creating(function ($c) {
            if (empty($c->contract_code)) $c->contract_code = self::generateCode();
            $c->updateStatus();
        });
    }
    public static function generateCode(): string {
        $prefix = 'AC-'.now()->format('Ym');
        $last = self::where('contract_code','like',$prefix.'-%')->orderBy('contract_code','desc')->first();
        $number = $last ? (int)substr($last->contract_code,-4)+1 : 1;
        return $prefix.'-'.str_pad($number,4,'0',STR_PAD_LEFT);
    }
    public function updateStatus(): void {
        $today = Carbon::today(); $expiring = Carbon::today()->addDays(30);
        if ($this->valid_until < $today) $this->status = 'expired';
        elseif ($this->valid_until <= $expiring) $this->status = 'expiring_soon';
        else $this->status = 'active';
    }
    public function items() { return $this->hasMany(ActivityItem::class); }
    public function createdBy() { return $this->belongsTo(User::class,'created_by'); }
}
