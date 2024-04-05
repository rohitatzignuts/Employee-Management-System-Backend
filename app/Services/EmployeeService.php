<?php

namespace App\Services;
use App\Models\Company;
use App\Models\User;

class EmployeeService
{
    public function generateUniqueEmployeeNumber(string $companyId)
    {
        $latestEmployee = User::where('company_id', $companyId)->latest('id')->first();

        if ($latestEmployee) {
            $latestEmployeeNumber = $latestEmployee->emp_number;
            $latestEmployeeNumberParts = explode('-', $latestEmployeeNumber);
            $latestEmployeeSuffix = end($latestEmployeeNumberParts);
            $nextEmployeeSuffix = (int) $latestEmployeeSuffix + 1;
            $employeeNumber = str_pad($nextEmployeeSuffix, strlen($latestEmployeeSuffix), '0', STR_PAD_LEFT);
            return $employeeNumber;
        }

        return '0001';
    }
}
