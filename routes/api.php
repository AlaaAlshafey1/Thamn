<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Api\QuestionController;
use App\Http\Controllers\Api\AnswerController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


// ------------------ AUTH ------------------
Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::middleware('auth:sanctum')->post('logout', [AuthController::class, 'logout']);
});


// ------------------ PUBLIC ENDPOINTS ------------------
Route::get('categories', [HomeController::class, 'categories']);


// ------------------ PROTECTED ENDPOINTS ------------------
Route::middleware('auth:sanctum')->group(function () {

    Route::get('questions/{id}', [HomeController::class, 'allQuestions']); // كل الأسئلة حسب الفئة
    Route::post('questions/{question_id}/answer', [AnswerController::class, 'store']);
});
