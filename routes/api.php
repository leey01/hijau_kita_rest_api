<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Client\BrowseController;
use App\Http\Controllers\Client\HomeController;
use App\Http\Controllers\Client\LeaderboardController;
use App\Http\Controllers\Client\ProfileController;
use App\Http\Controllers\Client\TrxController;
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

// Home
Route::group(['prefix' => 'home', 'middleware' => ['auth:api', 'verified']], function () {
    Route::get('/', [HomeController::class, 'index']);
    Route::get('/profile', [HomeController::class, 'profile']);
    Route::get('/activity-wishlist', [HomeController::class, 'activityWishlist']);
});

// Browse
Route::group(['prefix' => 'browse', 'middleware' => ['auth:api', 'verified']], function () {
    Route::post('/', [BrowseController::class, 'index']);
    Route::get('/list-categories', [BrowseController::class, 'listCategories']);
    Route::get('/list-sub-categories', [BrowseController::class, 'listSubCategories']);
    Route::get('/latest-activities', [BrowseController::class, 'latestActivities']);
    Route::get('/latest-events', [BrowseController::class, 'latestEvents']);
    Route::get('/detail-sub-category/{id}', [BrowseController::class, 'detailSubKategory']);
    Route::get('/detail-activity/{id}', [BrowseController::class, 'detailActivity']);
    Route::get('/detail-event/{id}', [BrowseController::class, 'detailEvent']);
    Route::get('/detail-quiz/{id}', [BrowseController::class, 'detailQuiz']);
});

// Trx
Route::group(['prefix' => 'trx', 'middleware' => ['auth:api', 'verified']], function () {
    Route::post('/activity', [TrxController::class, 'trxActivity']);
    Route::post('/event', [TrxController::class, 'trxEvent']);
    Route::post('/add-remove-wishlist/{id}', [TrxController::class, 'addRemoveActivityWishlist']);
    Route::post('/badge', [TrxController::class, 'trxBadge']);
});

// Leaderboard
Route::group(['prefix' => 'leaderboard', 'middleware' => ['auth:api', 'verified']], function () {
    Route::get('/', [LeaderboardController::class, 'index']);
    Route::get('/my-point', [LeaderboardController::class, 'myPoint']);
    Route::get('/list', [LeaderboardController::class, 'leaderboard']);
});

// Profile
Route::group(['prefix' => 'profile', 'middleware' => ['auth:api', 'verified']], function () {
    Route::get('/', [ProfileController::class, 'index']);
    Route::get('/done-activities', [ProfileController::class, 'doneActivities']);
    Route::get('/list-badges', [ProfileController::class, 'listBadges']);
    Route::get('/my-badges', [ProfileController::class, 'myBadge']);
});
