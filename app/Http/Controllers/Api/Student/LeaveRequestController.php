<?php

namespace App\Http\Controllers\Api\Student;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\AttendanceDetail;
use App\Models\LeaveRequest;
use App\Models\Semester;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class LeaveRequestController extends Controller
{
    /**
     * Get all leave requests
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $student = $request->user();

            if (!$student) {
                return $this->errorResponse('Unauthorized', 401);
            }

            $perPage = $request->get('per_page', 15);
            $status = $request->get('status'); // pending, approved, rejected
            $type = $request->get('type'); // izin, sakit

            $query = LeaveRequest::where('student_id', $student->id);

            // Filter by status
            if ($status) {
                $query->where('status', $status);
            }

            // Filter by type
            if ($type) {
                $query->where('type', $type);
            }

            $leaves = $query->orderByDesc('created_at')->paginate($perPage);

            // Transform data
            $data = $leaves->getCollection()->map(function ($leave) {
                return [
                    'id' => $leave->id,
                    'type' => $leave->type,
                    'reason' => $leave->reason,
                    'start_date' => $leave->start_date,
                    'end_date' => $leave->end_date,
                    'days_count' => Carbon::parse($leave->start_date)->diffInDays(Carbon::parse($leave->end_date)) + 1,
                    'status' => $leave->status,
                    'proof_file' => $leave->proof_file ? Storage::url($leave->proof_file) : null,
                    'response_note' => $leave->response_note,
                    'responded_at' => $leave->responded_at?->format('Y-m-d H:i:s'),
                    'responded_by' => $leave->respondedBy?->name,
                    'created_at' => $leave->created_at->format('Y-m-d H:i:s'),
                ];
            });

            return $this->successResponse([
                'current_page' => $leaves->currentPage(),
                'per_page' => $leaves->perPage(),
                'total' => $leaves->total(),
                'last_page' => $leaves->lastPage(),
                'from' => $leaves->firstItem(),
                'to' => $leaves->lastItem(),
                'data' => $data,
            ]);

        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Get single leave request detail
     */
    public function show(Request $request, int $id): JsonResponse
    {
        try {
            $student = $request->user();

            if (!$student) {
                return $this->errorResponse('Unauthorized', 401);
            }

            $leave = LeaveRequest::where('id', $id)
                ->where('student_id', $student->id)
                ->with(['respondedBy'])
                ->first();

            if (!$leave) {
                return $this->errorResponse('Leave request not found', 404);
            }

            return $this->successResponse([
                'id' => $leave->id,
                'type' => $leave->type,
                'type_label' => ucfirst($leave->type),
                'reason' => $leave->reason,
                'start_date' => $leave->start_date,
                'end_date' => $leave->end_date,
                'days_count' => Carbon::parse($leave->start_date)->diffInDays(Carbon::parse($leave->end_date)) + 1,
                'status' => $leave->status,
                'status_label' => $this->getStatusLabel($leave->status),
                'status_color' => $this->getStatusColor($leave->status),
                'proof_file' => $leave->proof_file ? Storage::url($leave->proof_file) : null,
                'response_note' => $leave->response_note,
                'responded_at' => $leave->responded_at?->format('Y-m-d H:i:s'),
                'responded_by' => $leave->respondedBy?->name,
                'created_at' => $leave->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $leave->updated_at->format('Y-m-d H:i:s'),
            ]);

        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Submit new leave request
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $student = $request->user();

            if (!$student) {
                return $this->errorResponse('Unauthorized', 401);
            }

            // Validation
            $validator = Validator::make($request->all(), [
                'type' => 'required|in:izin,sakit',
                'reason' => 'required|string|min:5|max:500',
                'start_date' => 'required|date|after_or_equal:today',
                'end_date' => 'nullable|date|after_or_equal:start_date',
                'proof_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120', // 5MB
            ], [
                'type.required' => 'Tipe izin harus dipilih',
                'type.in' => 'Tipe izin tidak valid',
                'reason.required' => 'Alasan harus diisi',
                'reason.min' => 'Alasan minimal 5 karakter',
                'reason.max' => 'Alasan maksimal 500 karakter',
                'start_date.required' => 'Tanggal mulai harus diisi',
                'start_date.after_or_equal' => 'Tanggal mulai tidak boleh di masa lalu',
                'end_date.after_or_equal' => 'Tanggal selesai harus setelah atau sama dengan tanggal mulai',
                'proof_file.mimes' => 'File harus berformat JPG, PNG, atau PDF',
                'proof_file.max' => 'Ukuran file maksimal 5MB',
            ]);

            if ($validator->fails()) {
                return $this->errorResponse(
                    'Validation failed',
                    422,
                    $validator->errors()->toArray()
                );
            }

            // Check for overlapping requests
            $overlapping = $this->checkOverlappingRequests(
                $student->id,
                $request->start_date,
                $request->end_date ?? $request->start_date
            );

            if ($overlapping) {
                return $this->errorResponse(
                    'Anda sudah memiliki pengajuan izin pada tanggal tersebut',
                    400,
                    ['overlapping' => true]
                );
            }

            $academicYear = AcademicYear::where('is_active', true)->first();
            $semester = Semester::where('is_active', true)->first();

            if (!$academicYear || !$semester) {
                return $this->errorResponse('Academic year atau semester tidak ditemukan', 400);
            }

            DB::beginTransaction();

            // Upload proof file
            $proofFilePath = null;
            if ($request->hasFile('proof_file')) {
                $proofFilePath = $this->uploadProofFile($request->file('proof_file'), $student->id);
            }

            // Create leave request
            $leave = LeaveRequest::create([
                'student_id' => $student->id,
                'grade_id' => $student->grade_id,
                'academic_year_id' => $academicYear->id,
                'semester_id' => $semester->id,
                'type' => $request->type,
                'reason' => $request->reason,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date ?? $request->start_date,
                'proof_file' => $proofFilePath,
                'status' => 'pending',
            ]);

            DB::commit();

            return $this->successResponse(
                [
                    'message' => 'Pengajuan izin berhasil dikirim dan menunggu persetujuan',
                    'data' => [
                        'id' => $leave->id,
                        'type' => $leave->type,
                        'start_date' => $leave->start_date,
                        'end_date' => $leave->end_date,
                        'status' => $leave->status,
                        'created_at' => $leave->created_at->format('Y-m-d H:i:s'),
                    ]
                ],
                201
            );

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Update leave request (only if pending)
     */
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $student = $request->user();

            if (!$student) {
                return $this->errorResponse('Unauthorized', 401);
            }

            $leave = LeaveRequest::where('id', $id)
                ->where('student_id', $student->id)
                ->first();

            if (!$leave) {
                return $this->errorResponse('Leave request not found', 404);
            }

            if ($leave->status !== 'pending') {
                return $this->errorResponse(
                    'Hanya izin dengan status pending yang dapat diubah',
                    400
                );
            }

            // Validation
            $validator = Validator::make($request->all(), [
                'type' => 'sometimes|in:izin,sakit',
                'reason' => 'sometimes|string|min:10|max:500',
                'start_date' => 'sometimes|date|after_or_equal:today',
                'end_date' => 'nullable|date|after_or_equal:start_date',
                'proof_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            ]);

            if ($validator->fails()) {
                return $this->errorResponse(
                    'Validation failed',
                    422,
                    $validator->errors()->toArray()
                );
            }

            DB::beginTransaction();

            $updateData = [];

            if ($request->has('type')) {
                $updateData['type'] = $request->type;
            }

            if ($request->has('reason')) {
                $updateData['reason'] = $request->reason;
            }

            if ($request->has('start_date')) {
                $updateData['start_date'] = $request->start_date;
            }

            if ($request->has('end_date')) {
                $updateData['end_date'] = $request->end_date;
            }

            // Upload new proof file if provided
            if ($request->hasFile('proof_file')) {
                // Delete old file
                if ($leave->proof_file) {
                    Storage::disk('public')->delete($leave->proof_file);
                }

                $updateData['proof_file'] = $this->uploadProofFile(
                    $request->file('proof_file'),
                    $student->id
                );
            }

            $leave->update($updateData);

            DB::commit();

            return $this->successResponse([
                'message' => 'Pengajuan izin berhasil diperbarui',
                'data' => [
                    'id' => $leave->id,
                    'type' => $leave->type,
                    'reason' => $leave->reason,
                    'start_date' => $leave->start_date,
                    'end_date' => $leave->end_date,
                    'status' => $leave->status,
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Delete/cancel leave request (only if pending)
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        try {
            $student = $request->user();

            if (!$student) {
                return $this->errorResponse('Unauthorized', 401);
            }

            $leave = LeaveRequest::where('id', $id)
                ->where('student_id', $student->id)
                ->first();

            if (!$leave) {
                return $this->errorResponse('Leave request not found', 404);
            }

            if ($leave->status !== 'pending') {
                return $this->errorResponse(
                    'Hanya izin dengan status pending yang dapat dibatalkan',
                    400
                );
            }

            DB::beginTransaction();

            // Delete proof file
            if ($leave->proof_file) {
                Storage::disk('public')->delete($leave->proof_file);
            }

            $leave->delete();

            DB::commit();

            return $this->successResponse([
                'message' => 'Pengajuan izin berhasil dibatalkan',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Get leave request statistics
     */
    public function statistics(Request $request): JsonResponse
    {
        try {
            $student = $request->user();

            if (!$student) {
                return $this->errorResponse('Unauthorized', 401);
            }

            $academicYear = AcademicYear::where('is_active', true)->first();

            $query = LeaveRequest::where('student_id', $student->id);

            if ($academicYear) {
                $query->where('academic_year_id', $academicYear->id);
            }

            $total = $query->count();
            $pending = $query->clone()->where('status', 'pending')->count();
            $approved = $query->clone()->where('status', 'approved')->count();
            $rejected = $query->clone()->where('status', 'rejected')->count();

            $izin = $query->clone()->where('type', 'izin')->where('status', 'approved')->count();
            $sakit = $query->clone()->where('type', 'sakit')->where('status', 'approved')->count();

            return $this->successResponse([
                'total' => $total,
                'pending' => $pending,
                'approved' => $approved,
                'rejected' => $rejected,
                'by_type' => [
                    'izin' => $izin,
                    'sakit' => $sakit,
                ],
                'academic_year' => $academicYear?->name,
            ]);

        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Check for overlapping leave requests
     */
    protected function checkOverlappingRequests(int $studentId, string $startDate, string $endDate): bool
    {
        return LeaveRequest::where('student_id', $studentId)
            ->where('status', '!=', 'rejected')
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('start_date', [$startDate, $endDate])
                    ->orWhereBetween('end_date', [$startDate, $endDate])
                    ->orWhere(function ($q) use ($startDate, $endDate) {
                        $q->where('start_date', '<=', $startDate)
                            ->where('end_date', '>=', $endDate);
                    });
            })
            ->exists();
    }

    /**
     * Upload proof file
     */
    protected function uploadProofFile($file, int $studentId): string
    {
        $filename = sprintf(
            '%s_%s.%s',
            $studentId,
            now()->format('YmdHis'),
            $file->getClientOriginalExtension()
        );

        $path = "leave_requests/" . now()->format('Y/m');

        return $file->storeAs($path, $filename, 'public');
    }

    /**
     * Get status label
     */
    protected function getStatusLabel(string $status): string
    {
        return match ($status) {
            'pending' => 'Menunggu',
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
            default => 'Unknown',
        };
    }

    /**
     * Get status color
     */
    protected function getStatusColor(string $status): string
    {
        return match ($status) {
            'pending' => 'warning',
            'approved' => 'success',
            'rejected' => 'danger',
            default => 'gray',
        };
    }

    /**
     * Success response
     */
    protected function successResponse($data, int $code = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $data,
        ], $code);
    }

    /**
     * Error response
     */
    protected function errorResponse(string $message, int $code = 400, array $errors = []): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $message,
        ];

        if (!empty($errors)) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $code);
    }
}