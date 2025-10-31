<?php

namespace App\Http\Controllers\Api\Student;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $setting = Setting::first();
        $academicYear = AcademicYear::where('is_active', true)->first();

        if ($setting) {
            // tambahkan atribut academic_year ke dalam setting
            $setting->academic_year = $academicYear?->start_year . '/' . $academicYear?->end_year . ' ' . $academicYear?->semester;
        }

        return response()->json($setting);
    }

}
