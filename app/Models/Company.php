<?php

namespace App\Models;
use App\Models\CompanyEmployee;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = ['name', 'cmp_email', 'logo', 'website', 'location', 'is_active'];

    public function user() : HasMany
    {
        return $this->hasMany(User::class);
    }
}
