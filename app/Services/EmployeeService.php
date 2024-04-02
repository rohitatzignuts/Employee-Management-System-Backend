<?php

namespace App\Services;
use App\Models\Company;
use App\Models\User;

class EmployeeService
{
    public function generateUniqueEmployeeNumber()
    {
        $employeeNumber = '0001'; // Start with a default employee number
        $latestEmployee = User::with('companyEmployees')->latest('id')->first();

        if ($latestEmployee && $latestEmployee->companyEmployees->isNotEmpty()) {
            $latestEmployeeNumber = $latestEmployee->companyEmployees->first()->emp_number;
            $latestEmployeeNumberParts = explode('-', $latestEmployeeNumber);
            $latestEmployeeSuffix = end($latestEmployeeNumberParts);
            $nextEmployeeSuffix = (int) $latestEmployeeSuffix + 1;
            $employeeNumber = str_pad($nextEmployeeSuffix, strlen($latestEmployeeSuffix), '0', STR_PAD_LEFT);
        }

        return $employeeNumber;
    }
}
