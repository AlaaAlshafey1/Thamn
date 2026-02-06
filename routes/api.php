<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\HomeController;
use App\Http\Controllers\API\QuestionController;
use App\Http\Controllers\API\AnswerController;
use App\Http\Controllers\Api\MarketPlaceOrderController;
use App\Http\Controllers\API\OrderController;
use App\Http\Controllers\API\PaymentController;
use App\Http\Controllers\API\SocialAuthController;
use App\Http\Controllers\API\SettingsController;
use App\Http\Controllers\API\MarketplaceController;
use App\Http\Controllers\API\FavoriteController;
use App\Http\Controllers\API\NotificationController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


// ------------------ AUTH ------------------
Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('check-phone', [AuthController::class, 'checkPhone']);
    Route::post('forgot-password', [AuthController::class, 'forgotPassword']);

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

// ------------------ MARKETPLACE ENDPOINTS ------------------
Route::get('marketplace/products', [MarketplaceController::class, 'index']);
Route::get('marketplace/products/{productId}', [MarketplaceController::class, 'show']);

// ------------------ SETTINGS ENDPOINTS ------------------
Route::prefix('settings')->group(function () {
    Route::get('colors', [SettingsController::class, 'colors']);
    Route::get('intro', [SettingsController::class, 'intro']);
    Route::get('home-steps', [SettingsController::class, 'homeSteps']);
    Route::get('about', [SettingsController::class, 'about']);
    Route::get('faq', [SettingsController::class, 'faq']);
    Route::get('contact', [SettingsController::class, 'contact']);
    Route::post('contact', [SettingsController::class, 'submitContact']);
    Route::get('terms', [SettingsController::class, 'terms']);
    Route::get('privacy', [SettingsController::class, 'privacy']);
});



// ------------------ PROTECTED ENDPOINTS ------------------
Route::middleware('auth:sanctum')->group(function () {

    // User profile endpoints
    Route::prefix('user')->group(function () {
        Route::get('profile', [AuthController::class, 'profile']);
        Route::post('profile/update', [AuthController::class, 'updateProfile']);
        Route::post('change-password', [AuthController::class, 'changePassword']);
        Route::delete('/delete-account', [AuthController::class, 'deleteAccount']);
    });

    // Protected settings endpoints
    Route::prefix('settings')->group(function () {
        Route::get('notifications', [SettingsController::class, 'notificationSettings']);
        Route::post('notifications', [SettingsController::class, 'updateNotificationSettings']);
    });

    Route::get('questions/{id}', [HomeController::class, 'allQuestions']); // كل الأسئلة حسب الفئة
    Route::get('marketplace/questions/{id}', [HomeController::class, 'marketQuestionsByGroup']); // كل الأسئلة حسب الفئة

    // Favorites endpoints
    Route::get('favorites', [FavoriteController::class, 'index']);
    Route::post('favorites', [FavoriteController::class, 'store']);
    Route::delete('favorites/{productId}', [FavoriteController::class, 'destroy']);

    // Notifications endpoints
    Route::get('notifications', [NotificationController::class, 'index']);
    Route::post('notifications/{id}/read', [NotificationController::class, 'markAsRead']);
    Route::post('notifications/read-all', [NotificationController::class, 'markAllAsRead']);

    Route::post('orders', [OrderController::class, 'store']);


    Route::post('valuation-orders/{order_id}', [OrderController::class, 'update']);
    Route::get('valuation-orders/{order_id}', [OrderController::class, 'show']);
    Route::delete('valuation-orders/{order_id}', [OrderController::class, 'destroy']);
    Route::post('valuation-orders/{orderId}/cancel', [OrderController::class, 'cancel']);
    Route::get('valuation-orders/{orderId}/result', [OrderController::class, 'result']);
    Route::post('valuation-orders/{orderId}/re-evaluate', [OrderController::class, 'reEvaluate']);
    Route::post('valuation-orders/{orderId}/resend', [OrderController::class, 'resendOrder']);
    Route::post('valuation-orders/{orderId}/send-to-market', [OrderController::class, 'sendToMarket']);


    Route::prefix('marketplace/orders')->group(function () {

        Route::post('/', [MarketPlaceOrderController::class, 'store']);

        Route::post('{orderId}', [MarketPlaceOrderController::class, 'update']);

        Route::get('{orderId}', [MarketPlaceOrderController::class, 'show']);

        Route::delete('{orderId}', [MarketPlaceOrderController::class, 'destroy']);

        Route::post('{orderId}/cancel', [MarketPlaceOrderController::class, 'cancel']);
    });

    Route::prefix('payment')->group(function () {

        Route::post('/order/{order_id}', [PaymentController::class, 'payOrder'])
            ->name('payment.order');

        // Tap server → server
    });
    Route::get('/test-ai/{orderId}', [PaymentController::class, 'testAiEvaluation']);


    Route::get('/payment/callback/package_sucess', [PaymentController::class, 'callback'])->name('payment.callback');
    Route::get('/payment/callback/package_error', [PaymentController::class, 'callback_error'])->name('payment.callback.failure');


    Route::prefix('orders')->group(function () {
        Route::get('/all', [OrderController::class, 'getOrders']);
    });

});
