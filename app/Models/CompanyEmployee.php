<?php

namespace App\Models;
use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyEmployee extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'cmp_id',
        'emp_number',
        'joining_date',
    ];

    public function company(){
        return $this->belongsTo(Company::class, 'foreign_key');
    }
}
