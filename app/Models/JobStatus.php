<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class JobStatus extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = ['user_id', 'status', 'resume', 'company_id', 'job_id', 'updated_by', 'created_by'];
    protected $table = 'job_status';

    /**
     * Get the company that owns the JobStatus
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id')->select('id', 'name');
    }

    /**
     * Get the job that owns the JobStatus
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class)->select('id','title');
    }

    /**
     * Get the user that owns the JobStatus
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

        protected static function boot()
    {
        parent::boot();

        static::creating(function ($job) {
            $job->created_by = Auth::id();
        });

        static::updating(function ($job) {
            $job->updated_by = Auth::id();
        });
    }
}
