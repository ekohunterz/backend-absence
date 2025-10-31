<?php

namespace App\Http\Controllers\Api\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;

class LogoutController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        try {
            // Cek apakah token masih valid
            $token = JWTAuth::getToken();
            if (!$token) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token tidak ditemukan.',
                ], 400);
            }

            // Jika token valid, lakukan invalidate
            JWTAuth::invalidate($token);

            return response()->json([
                'success' => true,
                'message' => 'Logout Berhasil!',
            ]);
        } catch (TokenExpiredException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Token sudah kedaluwarsa.',
            ], 401);
        } catch (TokenInvalidException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Token tidak valid.',
            ], 401);
        } catch (JWTException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Token tidak ditemukan atau tidak bisa diproses.',
            ], 401);
        }
    }
}
