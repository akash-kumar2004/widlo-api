<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\SchoolController;
use App\Http\Controllers\GatewayController;
use App\Http\Controllers\otp\GenrateotpController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
Route::middleware(['api.key'])->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'requestOtpLogin']);
    Route::post('verify-otp', [AuthController::class, 'verifyOtpLogin']);
    Route::post('resend-otp', [AuthController::class, 'resendOtp']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('add-post', [PostController::class, 'addPost']);
        Route::post('re-post', [PostController::class, 'rePost']);
        Route::post('post-status', [CommentController::class, 'postLikeOrDislike']);
        Route::get('all-post', [PostController::class, 'myAllPosts']);
        Route::get('post-like', [PostController::class, 'likedPosts']);
        Route::get('post-disklike', [PostController::class, 'disklikedPosts']);
        Route::get('near-by-post', [PostController::class, 'getNearbyPosts']);
        Route::get('near-by-post/{post_id}', [PostController::class, 'postDetails']);
        Route::get('categories', [CategoryController::class, 'index']);
        Route::post('add-category', [CategoryController::class, 'store']);
        Route::get('search-post', [PostController::class, 'searchPosts']);

        Route::post('logout', [AuthController::class, 'logout']);
    });
});

Route::middleware(['api.key'])->group(
    function () {
        Route::post('/phone_login', [GenrateotpController::class, 'sendotp']);
        Route::post('/verify_otp', [GenrateotpController::class, 'verify_otp']);
        Route::post('/email_login', [GenrateotpController::class, 'sendmail_otp']);
        Route::post('/verifymail_otp', [GenrateotpController::class, 'verifymail_otp']);
        // Route::group(['middleware' => "auth:sanctum"], function () {
        // });
    }
);
Route::get('/get_school_list', [SchoolController::class, 'school_list']);
Route::post('/update_gateway', [GatewayController::class, 'update_gateway']);
