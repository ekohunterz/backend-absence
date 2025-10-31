<?php

namespace App\Http\Controllers\Api\Student;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\LeaveRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LeaveRequestController extends Controller
{
    public function index(Request $request)
    {
        $student = auth('api')->user();

        $leaves = LeaveRequest::where('student_id', $student->id)
            ->orderByDesc('id')
            ->paginate(10);

        return response()->json($leaves);
    }

    public function store(Request $request)
    {
        $student = auth('api')->user();
        $academicYear = AcademicYear::where('is_active', true)->first();

        $validator = Validator::make($request->all(), [
            'type' => 'required|in:izin,sakit,alpa',
            'reason' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'proof_file' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Upload file jika ada
        $proof_filePath = null;
        if ($request->hasFile('proof_file')) {
            $file = $request->file('proof_file');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('leave_requests', $filename, 'public');
            $proof_filePath = 'leave_requests/' . $filename;
        }

        $leave = LeaveRequest::create([
            'student_id' => $student->id,
            'grade_id' => $student->grade_id,
            'academic_year_id' => $academicYear->id,
            'type' => $request->type,
            'reason' => $request->reason,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date ?? $request->start_date,
            'proof_file' => $proof_filePath,
            'status' => 'pending',
        ]);

        return response()->json([
            'message' => 'Pengajuan izin berhasil dikirim',
            'data' => $leave,
        ], 201);
    }
}
