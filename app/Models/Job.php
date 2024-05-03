<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Job extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = ['title', 'description', 'location', 'pay', 'company_id', 'is_active', 'is_trending', 'created_by', 'updated_by'];
    protected static function newFactory()
    {
        return \Database\Factories\JobFactory::new();
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class)->select('id', 'name', 'logo');
    }

    /**
     * Get all of the jobStatus for the Job
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function jobStatuses(): HasMany
    {
        return $this->hasMany(JobStatus::class);
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
