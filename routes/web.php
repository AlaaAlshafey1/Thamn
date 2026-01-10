<?php

use App\Http\Controllers\AppPageController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\QuestionStepController;
use App\Http\Controllers\TapPaymentController;
use App\Http\Controllers\TermConditionController;
use App\Http\Controllers\API\PaymentController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/payment/order/{orderId}',
    [PaymentController::class, 'redirect']
)->name('payment.redirect');


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::resource('roles', RoleController::class);
    Route::post('roles/import', [RoleController::class, 'import'])->name('roles.import');
    Route::get('roles/export', [RoleController::class, 'export'])->name('roles.export');
    Route::resource('users', \App\Http\Controllers\UserController::class);
    Route::resource('categories', CategoryController::class);
    Route::resource('questions', QuestionController::class);
    Route::resource('app_pages', AppPageController::class);
    Route::resource('terms', TermConditionController::class);
    Route::resource('question_steps', QuestionStepController::class);
    Route::get('orders', [OrderController::class, 'index'])
        ->name('orders.index');

    Route::get('orders/create', [OrderController::class, 'create'])
        ->name('orders.create');

    Route::get('orders/{order}', [OrderController::class, 'show'])
        ->name('orders.show');

    Route::post('orders/{order}/status', [OrderController::class, 'updateStatus'])
        ->name('orders.updateStatus');

    Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
    Route::post('orders/{order}/evaluate', [OrderController::class,'expertEvaluate'])->name('orders.expert.evaluate');
    Route::post('/orders/{order}/price', [OrderController::class, 'updatePrice'])
        ->name('orders.updatePrice');

    Route::get('payments', [TapPaymentController::class, 'index'])->name('payments.index');
    Route::get('payments/{payment}', [TapPaymentController::class, 'show'])->name('payments.show');
    Route::delete( 'payments/{payment}', [TapPaymentController::class, 'destroy'])->name('payments.destroy');

    Route::post('/orders/assign-expert', [OrderController::class, 'assignExpert'])->name('orders.assignExpert');

    Route::post('/orders/{order}/thamn-evaluate',
        [OrderController::class, 'thamnEvaluate']
    )->name('orders.thamn.evaluate');

});
Route::get('lang/{locale}', function ($locale) {
    if (in_array($locale, ['ar', 'en'])) {
        session(['locale' => $locale]);
        app()->setLocale($locale);
    }
    return redirect()->back();
})->name('change.language');

require __DIR__.'/auth.php';
