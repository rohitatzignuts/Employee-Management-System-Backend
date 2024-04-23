<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobStatus extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'status', 'resume', 'company_id','job_id'];
    protected $table = 'job_status';
}
