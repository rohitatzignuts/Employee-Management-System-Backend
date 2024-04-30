<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = ['name', 'cmp_email', 'logo', 'website', 'location', 'is_active','created_by','updated_by'];

    public function users()
    {
        return $this->hasMany(User::class);
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
}
