<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes (stub - sanctum ready)
|--------------------------------------------------------------------------
| Sanctum is not installed on Hostinger shared hosting by default.
| To enable: composer require laravel/sanctum && php artisan vendor:publish --provider="Laravel\\Sanctum\\SanctumServiceProvider"
| Then uncomment the auth:sanctum group below.
*/

Route::prefix('v1')->name('api.v1.')->group(function () {
    Route::get('/health', fn () => response()->json(['ok' => true]))->name('health');

    // Public v1 endpoints (candidates/courses lookups) can be added here.
    // Route::middleware('auth:sanctum')->group(function () {
    //     Route::apiResource('candidates', \App\Http\Controllers\Api\CandidateApiController::class);
    //     Route::apiResource('applications', \App\Http\Controllers\Api\ApplicationApiController::class);
    // });
});
