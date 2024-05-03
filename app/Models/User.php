<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['first_name', 'last_name', 'email', 'password', 'role', 'company_id', 'emp_number', 'joining_date', 'created_by', 'updated_by'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = ['password', 'remember_token'];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class)->select('id', 'name');
    }

    /**
     * Get all of the jobStatus for the User
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
