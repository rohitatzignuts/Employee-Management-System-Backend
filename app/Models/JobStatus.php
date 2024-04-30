<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JobStatus extends Model
{
    use HasFactory,SoftDeletes;
    protected $fillable = ['user_id', 'status', 'resume', 'company_id','job_id','updated_by','created_by'];
    protected $table = 'job_status';
}
