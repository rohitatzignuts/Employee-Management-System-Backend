<?php

namespace App\Models;
use App\Models\CompanyEmployee;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'cmp_email',
        'logo',
        'website',
        'location',
    ];

    public function companyEmployee(){
        return $this->hasMany(CompanyEmployee::class);
    }
}
