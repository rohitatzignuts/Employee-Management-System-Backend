<?php

namespace App\Services;
use App\Models\Company;
use App\Models\User;
use App\Models\Preferences;

class EmployeeService
{
    public function generateUniqueEmployeeNumber()
    {
        $preferences = Preferences::where('code', 'EMP')->first();

        if (!$preferences) {
            $preferences = Preferences::create(['code' => 'EMP', 'value' => 1]);
        }

        $nextEmpNumber = $preferences->value;
        $employeeNumber = 'EMP-' . str_pad($nextEmpNumber, strlen((string)$nextEmpNumber), '0', STR_PAD_LEFT);

        $preferences->update(['value' => $nextEmpNumber + 1]);

        return $employeeNumber;
    }
}
