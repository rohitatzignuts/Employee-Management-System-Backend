<?php
namespace App\Helpers;
use App\Models\Preferences;

if (!function_exists('generateUniqueEmployeeNumber')) {
    function generateUniqueEmployeeNumber()
    {
        // Retrieve preferences for employee numbers, or create them if they don't exist
        $preferences = Preferences::firstOrCreate(['code' => 'EMP'], ['value' => 1]);

        // Generate the next employee number
        $employeeNumber = 'EMP-' . str_pad($preferences->value, 4, '0', STR_PAD_LEFT);

        // Increment the preferences for the next employee number
        $preferences->update(['value' => $preferences->value + 1]);

        return $employeeNumber;
    }
}
