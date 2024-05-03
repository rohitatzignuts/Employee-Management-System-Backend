<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Company extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = ['name', 'cmp_email', 'logo', 'website', 'location', 'is_active', 'created_by', 'updated_by'];

    public function users(): HasMany
    {
        return $this->hasMany(User::class,'company_id');
    }

    public function jobs()
    {
        return $this->hasMany(Job::class);
    }

    public function companyAdmin()
    {
        return $this->hasOne(User::class)
            ->where('role', 'cmp_admin')
            ->latest('id');
    }

    /**
     * Get all of the jobApplications for the Company
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function jobStatus(): HasMany
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
