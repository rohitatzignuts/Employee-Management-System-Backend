<?php

namespace App\Services;
use App\Models\Company;
use App\Models\User;
use App\Models\Preferences;

class EmployeeService
{
    public function generateUniqueEmployeeNumber()
    {
        // Retrieve preferences for employee numbers
        $preferences = Preferences::where('code', 'EMP')->first();

        // If preferences do not exist, create them
        if (!$preferences) {
            $preferences = Preferences::create(['code' => 'EMP', 'value' => 1]);
        }

        // Generate the next employee number
        $nextEmpNumber = $preferences->value;
        $employeeNumber = 'EMP-' . str_pad($nextEmpNumber, strlen((string) $nextEmpNumber), '0', STR_PAD_LEFT);

        // Update preferences for the next employee number
        $preferences->update(['value' => $nextEmpNumber + 1]);

        return $employeeNumber;
    }
}
