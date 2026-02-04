<?php

use App\Http\Controllers\Admin\AboutController;
use App\Http\Controllers\Admin\FaqController;
use App\Http\Controllers\AppPageController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WithdrawalController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\QuestionStepController;
use App\Http\Controllers\TapPaymentController;
use App\Http\Controllers\TermConditionController;
use App\Http\Controllers\API\PaymentController;
use App\Http\Controllers\ColorController;
use App\Http\Controllers\ContactController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/payment/order/{orderId}',
    [PaymentController::class, 'redirect']
)->name('payment.redirect');

Route::get('/payment/callback/package_sucess', [PaymentController::class, 'callback'])->name('payment.callback');
Route::get('/payment/callback/package_error', [PaymentController::class, 'callback_error'])->name('payment.callback.failure');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::resource('roles', RoleController::class);
    Route::post('roles/import', [RoleController::class, 'import'])->name('roles.import');
    Route::get('roles/export', [RoleController::class, 'export'])->name('roles.export');
    Route::resource('users', controller: \App\Http\Controllers\UserController::class);

    Route::prefix('experts')->group(function () {
        Route::get('/', [UserController::class, 'experts'])->name('experts.index');
        Route::get('/create', [UserController::class, 'createExpert'])->name('experts.create');
        Route::get('/{user}', [\App\Http\Controllers\UserController::class, 'showExpert'])
            ->name('experts.show');
        Route::post('/store', [UserController::class, 'storeExpert'])->name('experts.store');
        Route::get('/{user}/edit', [UserController::class, 'editExpert'])->name('experts.edit');
        Route::put('/{user}', [UserController::class, 'updateExpert'])->name('experts.update');
    });
        // Expert Routes
        Route::get('/withdrawals/create', [WithdrawalController::class, 'create'])->name('withdrawals.create');
        Route::post('/withdrawals', [WithdrawalController::class, 'store'])->name('withdrawals.store');
        Route::get('/withdrawals/my', [WithdrawalController::class, 'myWithdrawals'])->name('withdrawals.my');

        // Admin Routes
        Route::get('/withdrawals', [WithdrawalController::class, 'index'])->name('withdrawals.index');
        Route::post('/withdrawals/{id}/approve', [WithdrawalController::class, 'approve'])->name('withdrawals.approve');
        Route::post('/withdrawals/{id}/reject', [WithdrawalController::class, 'reject'])->name('withdrawals.reject');


    Route::resource('categories', CategoryController::class);
    Route::resource('questions', QuestionController::class);
    Route::resource('app_pages', AppPageController::class);
    Route::resource('terms', TermConditionController::class);
    Route::resource('question_steps', QuestionStepController::class);
    Route::resource('contacts', ContactController::class);
    Route::prefix('pages')->group(function() {
        Route::get('{type?}', [PageController::class, 'index'])->name('pages.index');
        Route::get('create/{type?}', [PageController::class, 'create'])->name('pages.create');
        Route::post('store', [PageController::class, 'store'])->name('pages.store');
        Route::get('edit/{id}', [PageController::class, 'edit'])->name('pages.edit');
        Route::put('update/{id}', [PageController::class, 'update'])->name('pages.update');
        Route::delete('destroy/{id}', [PageController::class, 'destroy'])->name('pages.destroy');
    });
    Route::resource('faqs', FaqController::class);
    Route::resource('colors', ColorController::class);



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

    Route::post('/orders/{order}/ai-evaluate', [OrderController::class, 'aiEvaluate'])
        ->name('orders.ai.evaluate');


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
