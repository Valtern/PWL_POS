<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class LogoutController extends Controller
{
    public function __invoke(Request $request)
    {
        try {
            // Remove token
            $removeToken = JWTAuth::invalidate(JWTAuth::getToken());

            if ($removeToken) {
                // Return response JSON
                return response()->json([
                    'success' => true,
                    'message' => 'Logout Berhasil!'
                ]);
            }
        } catch (JWTException $e) {
            // Return error response
            return response()->json([
                'success' => false,
                'message' => 'Logout Gagal!',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
