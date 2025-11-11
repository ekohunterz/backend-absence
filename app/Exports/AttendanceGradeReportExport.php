<?php

namespace App\Exports;

use App\Models\Setting;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;


class AttendanceGradeReportExport implements FromView
{
    public $reports;
    public $month;
    public $academic_year;
    public Setting $setting;


    public function __construct($reports, $month, $academic_year)
    {
        $this->reports = $reports;
        $this->month = $month;
        $this->academic_year = $academic_year;
        $this->setting = Setting::first();
    }

    public function view(): View
    {
        return view('exports.attendance-grade-report', [
            'reports' => $this->reports,
            'month' => $this->month,
            'academic_year' => $this->academic_year,
            'setting' => $this->setting
        ]);
    }
}
