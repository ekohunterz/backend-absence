<?php

namespace App\Http\Controllers\Api\Student;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\AttendanceDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{


    public function today(Request $request)
    {
        $student = auth('api')->user();
        $today = Carbon::today();

        $attendance = Attendance::where('grade_id', $student->grade_id)
            ->whereDate('presence_date', $today)
            ->with(['details' => fn($q) => $q->where('student_id', $student->id)])
            ->first();

        if (!$attendance) {
            return response()->json(['message' => 'Belum ada absensi hari ini'], 404);
        }

        $detail = $attendance->details->first();

        return response()->json([
            'date' => $attendance->presence_date,
            'start_time' => $attendance->start_time,
            'end_time' => $attendance->end_time,
            'status' => $detail->status ?? 'belum absen',
            'check_in_time' => $detail->check_in_time,
            'check_out_time' => $detail->check_out_time,
        ]);
    }

    public function checkIn(Request $request)
    {
        $student = auth('api')->user();
        $today = Carbon::today();

        if ($student->is_present($today)) {
            return response()->json(['message' => 'Anda sudah absen hari ini'], 400);
        }

        $request->validate([
            'photo_in' => 'required|image|mimes:jpg,jpeg,png|max:2048',
            'location' => 'nullable|string',
        ]);

        if ($request->has('photo_in')) {
            $photo_in = $request->file('photo_in');
            $photo_in_name = time() . '.' . $photo_in->getClientOriginalExtension();
            $photo_in_path = $photo_in->storeAs('attendance', $photo_in_name, 'public');
        }

        $attendance = Attendance::firstOrCreate(
            [
                'grade_id' => $student->grade_id,
                'presence_date' => $today,
            ],
            [
                'start_time' => now()->format('H:i:s'),
                'end_time' => now()->addHours(8)->format('H:i:s'),
                'academic_year_id' => AcademicYear::where('is_active', true)->first()->id
            ]
        );

        $detail = AttendanceDetail::updateOrCreate(
            [
                'attendance_id' => $attendance->id,
                'student_id' => $student->id,
            ],
            [
                'status' => 'hadir',
                'check_in_time' => now(),
                'location' => $request->location ?? null,
                'photo_in' => $photo_in_path ?? null
            ]
        );

        return response()->json(['message' => 'Check-in berhasil', 'data' => $detail]);
    }

    public function checkOut(Request $request)
    {
        $student = auth('api')->user();
        $today = Carbon::today();


        $request->validate([
            'photo_out' => 'required|image|mimes:jpg,jpeg,png|max:2048',
            'location' => 'nullable|string',
        ]);

        if ($request->has('photo_out')) {
            $photo_out = $request->file('photo_out');
            $photo_out_name = time() . '.' . $photo_out->getClientOriginalExtension();
            $photo_out_path = $photo_out->storeAs('attendance', $photo_out_name, 'public');
        }

        $attendance = Attendance::where('grade_id', $student->grade_id)
            ->whereDate('presence_date', $today)
            ->first();

        if (!$attendance) {
            return response()->json(['message' => 'Belum ada absensi hari ini'], 404);
        }

        $detail = AttendanceDetail::where('attendance_id', $attendance->id)
            ->where('student_id', $student->id)
            ->first();

        if (!$detail) {
            return response()->json(['message' => 'Belum melakukan check-in'], 400);
        }

        if ($detail->check_out_time) {
            return response()->json(['message' => 'Anda sudah absen hari ini'], 400);
        }

        $detail->update([
            'check_out_time' => now(),
            'photo_out' => $photo_out_path ?? null,
        ]);

        return response()->json(['message' => 'Check-out berhasil', 'data' => $detail]);
    }

    public function history(Request $request)
    {
        $student = auth('api')->user();

        $perPage = $request->get('per_page', 10); // default 10 item per halaman

        $details = AttendanceDetail::with('attendance')
            ->where('student_id', $student->id)
            ->orderByDesc('id')
            ->paginate($perPage);

        // transformasi data agar lebih rapi
        $data = $details->getCollection()->map(fn($d) => [
            'presence_date' => optional($d->attendance)->presence_date,
            'status' => $d->status,
            'check_in_time' => $d->check_in_time,
            'check_out_time' => $d->check_out_time,
        ]);

        // ubah koleksi yang sudah di-map kembali ke paginator
        $details->setCollection($data);

        return response()->json([
            'current_page' => $details->currentPage(),
            'per_page' => $details->perPage(),
            'total' => $details->total(),
            'last_page' => $details->lastPage(),
            'data' => $details->items(),
        ]);
    }

}
