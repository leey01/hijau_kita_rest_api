<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\EmailVerificationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// jika token user tidak valid
Route::get('/', function () {
    return response()->json([
        'message' => 'authentication required'
    ], 401);
})->name('login');

// jika user blom verify akun
Route::get('/verifiy-notice', function () {
    return response()->json([
        'message' => 'user not verify'
    ], 403);
})->name('verification.notice');

// register & login
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/logout', [AuthController::class, 'logout'])->middleware(['auth:api']);

//make route group with prefix email
Route::group(['prefix' => 'email', 'middleware' => ['auth:api']], function () {
    // verify email
    Route::post('/verification', [EmailVerificationController::class, 'email_verification']);
    // send email verification
    Route::get('/verification/send', [EmailVerificationController::class, 'sendEmailVerification']);
});
