<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class ImportLog extends Model {
    use HasUuids;
    protected $fillable = ['module','filename','total_rows','success_rows','failed_rows','errors','status','created_by'];
}
