<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\HomeController;
use App\Http\Controllers\API\QuestionController;
use App\Http\Controllers\API\AnswerController;
use App\Http\Controllers\API\OrderController;
use App\Http\Controllers\API\SocialAuthController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


// ------------------ AUTH ------------------
Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('check-phone', [AuthController::class, 'checkPhone']);

    Route::post('verifyOtp', [AuthController::class, 'verifyOtp']);
    Route::post('resendOtp', [AuthController::class, 'resendOtp']);

    //Social
    Route::post('social/check', [SocialAuthController::class, 'checkSocialAccount']);
    Route::post('social/register', [SocialAuthController::class, 'registerSocialAccount']);

    Route::middleware('auth:sanctum')->post('logout', [AuthController::class, 'logout']);
});


// ------------------ PUBLIC ENDPOINTS ------------------
Route::get('categories', [HomeController::class, 'categories']);
Route::get('terms', [HomeController::class, 'terms']);


// ------------------ PROTECTED ENDPOINTS ------------------
Route::middleware('auth:sanctum')->group(function () {

    Route::prefix('user')->group(function () {

    Route::get('profile', [AuthController::class, 'profile']);
    Route::post('profile/update', [AuthController::class, 'updateProfile']);
    });

    Route::get('questions/{id}', [HomeController::class, 'allQuestions']); // كل الأسئلة حسب الفئة
    Route::post('orders', [OrderController::class, 'store']);
});
