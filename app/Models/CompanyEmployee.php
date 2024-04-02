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
        'company_id',
        'emp_number',
        'joining_date',
    ];

    public function company(){
        return $this->belongsTo(Company::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
