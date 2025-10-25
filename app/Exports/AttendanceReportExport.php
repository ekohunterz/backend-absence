<?php

namespace App\Exports;


use App\Models\Grade;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class AttendanceReportExport implements FromView
{

    public $reports;
    public Grade $grade;
    public $month;
    public $academic_year;


    public function __construct($reports, $grade, $month, $academic_year)
    {
        $this->reports = $reports;
        $this->grade = Grade::findOrFail($grade);
        $this->month = $month;
        $this->academic_year = $academic_year;
    }

    public function view(): View
    {
        return view('exports.attendance-report', [
            'reports' => $this->reports,
            'grade' => $this->grade,
            'month' => $this->month,
            'academic_year' => $this->academic_year
        ]);
    }
}
