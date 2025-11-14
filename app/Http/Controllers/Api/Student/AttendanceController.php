<?php

namespace App\Http\Controllers\Api\Student;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\AttendanceDetail;
use App\Models\Semester;
use App\Models\Setting;
use App\Services\PresenceService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class AttendanceController extends Controller
{

    protected PresenceService $presenceService;

    public function boot(PresenceService $presenceService): void
    {
        $this->presenceService = $presenceService;
    }

    /**
     * Get today's attendance status
     */
    public function today(Request $request): JsonResponse
    {
        try {
            $student = $request->user('student');

            if (!$student) {
                return $this->errorResponse('Unauthorized', 401);
            }

            $attendance = $this->presenceService->getTodayPresence($student);

            if (!$attendance) {
                return $this->successResponse([
                    'status' => 'not_present',
                    'message' => 'Anda belum melakukan absensi hari ini',
                    'date' => $attendance->presence_date,
                    'check_in_time' => null,
                    'check_out_time' => null,
                ]);
            }

            return $this->successResponse([
                'status' => $attendance->status,
                'date' => $attendance->created_at,
                'check_in_time' => $attendance->check_in_time?->format('H:i:s'),
                'check_out_time' => $attendance->check_out_time?->format('H:i:s'),
                'photo_in' => $attendance->photo_in ? Storage::url($attendance->photo_in) : null,
                'photo_out' => $attendance->photo_out ? Storage::url($attendance->photo_out) : null,
                'can_check_in' => !$attendance->check_in_time && !$attendance->check_out_time,
                'can_check_out' => $attendance->check_in_time && !$attendance->check_out_time,
            ]);

        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Check In
     */
    public function checkIn(Request $request): JsonResponse
    {
        try {
            $student = $request->user('student');

            if (!$student) {
                return $this->errorResponse('Unauthorized', 401);
            }

            // Validation
            $validator = Validator::make($request->all(), [
                'photo' => 'required|image|mimes:jpg,jpeg,png|max:5120', // 5MB
                'latitude' => 'required|numeric|between:-90,90',
                'longitude' => 'required|numeric|between:-180,180',
            ]);

            if ($validator->fails()) {
                return $this->errorResponse(
                    'Validation failed',
                    422,
                    $validator->errors()->toArray()
                );
            }

            // Check if already checked in today

            if ($this->presenceService->getTodayPresence($student)) {
                return $this->errorResponse('Anda sudah melakukan presensi hari ini', 400);
            }
            $leaveRequest = $this->presenceService->getPermissionStatus($student);

            if ($leaveRequest) {
                return $this->errorResponse('Anda sudah tercatat' . $leaveRequest->type . ' hari ini', 400);
            }

            // Validate location (radius check)

            $locationValid = $this->presenceService->validateLocation(
                $request->latitude,
                $request->longitude,
            );
            if (!$locationValid) {
                return $this->errorResponse("Anda berada di luar radius presensi.", 400);
            }


            DB::beginTransaction();

            // Upload photo
            $photoPath = $this->presenceService->savePhoto($request->file('photo'), 'check-in', $student->id);

            // Get or create attendance record
            $attendance = AttendanceDetail::updateOrCreate(
                [
                    'attendance_id' => $this->presenceService->getOrCreateAttendance($student)->id,
                    'student_id' => $student->id,
                ],
                [
                    'status' => 'hadir',
                    'check_in_time' => now(),
                    'location_in' => $request->latitude . ',' . $request->longitude,
                    'photo_in' => $photoPath,
                ]
            );

            DB::commit();

            return $this->successResponse(
                [
                    'message' => 'Check-in berhasil',
                    'detail' => [
                        'status' => $attendance->status,
                        'check_in_time' => $attendance->check_in_time->format('H:i:s'),
                        'photo_in' => Storage::url($attendance->photo_in),
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
     * Check Out
     */
    public function checkOut(Request $request): JsonResponse
    {
        try {
            $student = $request->user('student');

            if (!$student) {
                return $this->errorResponse('Unauthorized', 401);
            }

            // Validation
            $validator = Validator::make($request->all(), [
                'photo' => 'required|image|mimes:jpg,jpeg,png|max:5120',
                'latitude' => 'required|numeric|between:-90,90',
                'longitude' => 'required|numeric|between:-180,180',
            ]);

            if ($validator->fails()) {
                return $this->errorResponse(
                    'Validation failed',
                    422,
                    $validator->errors()->toArray()
                );
            }

            // Get today's attendance
            $attendance = $this->presenceService->getTodayPresence($student);

            if (!$attendance) {
                return $this->errorResponse('Anda sudah melakukan presensi hari ini', 400);
            }
            $leaveRequest = $this->presenceService->getPermissionStatus($student);

            if ($leaveRequest) {
                return $this->errorResponse('Anda sudah tercatat' . $leaveRequest->type . ' hari ini', 400);
            }

            // Check if not checked in yet
            if (!$attendance->check_in_time) {
                return $this->errorResponse('Anda belum melakukan check-in', 400);
            }

            // Check if already checked out
            if ($attendance->check_out_time) {
                return $this->errorResponse('Anda sudah melakukan check-out', 400);
            }

            // Validate location
            $locationValid = $this->presenceService->validateLocation(
                $request->latitude,
                $request->longitude,
            );
            if (!$locationValid) {
                return $this->errorResponse("Anda berada di luar radius presensi.", 400);
            }

            DB::beginTransaction();

            // Upload photo
            $photoPath = $this->presenceService->savePhoto($request->file('photo'), 'check-out', $student->id);

            // Update attendance detail
            $attendance->update([
                'check_out_time' => now(),
                'location_out' => $request->latitude . ',' . $request->longitude,
                'photo_out' => $photoPath,
            ]);

            DB::commit();

            return $this->successResponse([
                'message' => 'Check-out berhasil',
                'detail' => [
                    'status' => $attendance->status,
                    'check_in_time' => $attendance->check_in_time->format('H:i:s'),
                    'check_out_time' => $attendance->check_out_time->format('H:i:s'),
                    'photo_out' => Storage::url($attendance->photo_out),
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Get attendance history
     */
    public function history(Request $request): JsonResponse
    {
        try {
            $student = $request->user('student');

            if (!$student) {
                return $this->errorResponse('Unauthorized', 401);
            }

            $perPage = $request->get('per_page', 15);
            $month = $request->get('month'); // Format: YYYY-MM
            $status = $request->get('status'); // hadir, izin, sakit, alpa

            $query = AttendanceDetail::with([
                'attendance' => function ($q) {
                    $q->select('id', 'presence_date', 'grade_id');
                }
            ])
                ->where('student_id', $student->id);

            // Filter by month
            if ($month) {
                $query->whereHas('attendance', function ($q) use ($month) {
                    $q->whereYear('presence_date', substr($month, 0, 4))
                        ->whereMonth('presence_date', substr($month, 5, 2));
                });
            }

            // Filter by status
            if ($status) {
                $query->where('status', $status);
            }

            $details = $query->orderBy('id', 'desc')->paginate($perPage);

            // Transform data
            $data = $details->getCollection()->map(function ($detail) {
                return [
                    'id' => $detail->id,
                    'date' => $detail->attendance->presence_date,
                    'status' => $detail->status,
                    'check_in_time' => $detail->check_in_time?->format('H:i:s'),
                    'check_out_time' => $detail->check_out_time?->format('H:i:s'),
                    'photo_in' => $detail->photo_in ? Storage::url($detail->photo_in) : null,
                    'photo_out' => $detail->photo_out ? Storage::url($detail->photo_out) : null,
                    'notes' => $detail->notes,
                ];
            });

            return $this->successResponse([
                'current_page' => $details->currentPage(),
                'per_page' => $details->perPage(),
                'total' => $details->total(),
                'last_page' => $details->lastPage(),
                'from' => $details->firstItem(),
                'to' => $details->lastItem(),
                'data' => $data,
            ]);

        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Get attendance statistics
     */
    public function statistics(Request $request): JsonResponse
    {
        try {
            $student = $request->user('student');

            if (!$student) {
                return $this->errorResponse('Unauthorized', 401);
            }

            $month = $request->get('month', now()->format('Y-m'));

            $query = AttendanceDetail::whereHas('attendance', function ($q) use ($student, $month) {
                $q->where('grade_id', $student->grade_id)
                    ->whereYear('presence_date', substr($month, 0, 4))
                    ->whereMonth('presence_date', substr($month, 5, 2));
            })->where('student_id', $student->id);

            $total = $query->count();
            $hadir = $query->clone()->where('status', 'hadir')->count();
            $izin = $query->clone()->where('status', 'izin')->count();
            $sakit = $query->clone()->where('status', 'sakit')->count();
            $alpa = $query->clone()->where('status', 'alpa')->count();

            $attendanceRate = $total > 0 ? round(($hadir / $total) * 100, 2) : 0;

            return $this->successResponse([
                'month' => $month,
                'total_days' => $total,
                'hadir' => $hadir,
                'izin' => $izin,
                'sakit' => $sakit,
                'alpa' => $alpa,
                'attendance_rate' => $attendanceRate,
            ]);

        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
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