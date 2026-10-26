<?php

use App\Http\Controllers\Api\AuthController;
use App\Models\Asset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Public authentication routes
Route::post('/login', [AuthController::class, 'login'])
    ->middleware('throttle:5,1');

Route::post('/mobile-login', [AuthController::class, 'mobileLogin'])
    ->middleware('throttle:5,1');

// Protected API routes
Route::middleware('auth:sanctum')->group(function () {

    Route::get('/user', [AuthController::class, 'me']);

    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/assets', function (Request $request) {
        $user = $request->user();

        if (
            ! ($user->currentAccessToken() instanceof \Laravel\Sanctum\PersonalAccessToken) ||
            ! $user->tokenCan('server:read') ||
            ! $user->can('viewAny', Asset::class)
        ) {
            return response()->json([
                'message' => 'Bu işlem için yetkiniz yok.',
            ], 403);
        }

        return response()->json([
            'data' => Asset::query()
                ->select([
                    'id',
                    'name',
                    'type',
                    'serial_number',
                    'is_available',
                ])
                ->orderBy('id')
                ->limit(50)
                ->get(),
        ]);
    });
});