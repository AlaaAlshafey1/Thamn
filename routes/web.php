<?php

use App\Http\Controllers\Admin\AboutController;
use App\Http\Controllers\Admin\FaqController;
use App\Http\Controllers\AppPageController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WithdrawalController;
use App\Http\Controllers\RefundRequestController;
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
use App\Http\Controllers\HomeStepController;
use App\Http\Controllers\IntroController;
use App\Http\Controllers\BannerController;

use App\Http\Controllers\ArbitratorDeclarationController;

Route::get('/', [App\Http\Controllers\PublicPageController::class, 'index'])->name('home');

// Public Pages for App Store Compliance
Route::get('/privacy-policy', [App\Http\Controllers\PublicPageController::class, 'privacy'])->name('public.privacy');
Route::get('/terms-conditions', [App\Http\Controllers\PublicPageController::class, 'terms'])->name('public.terms');
Route::get('/about-us', [App\Http\Controllers\PublicPageController::class, 'about'])->name('public.about');
Route::get('/contact-us', [App\Http\Controllers\PublicPageController::class, 'contact'])->name('public.contact');
Route::post('/contact-us', [App\Http\Controllers\PublicPageController::class, 'submitContact'])->name('public.contact.submit');

// Expert Registration
Route::get('/experts/agreement/preview', [App\Http\Controllers\ExpertRegistrationController::class, 'previewAgreement'])->name('experts.agreement.preview');
Route::get('/experts/register', [App\Http\Controllers\ExpertRegistrationController::class, 'showForm'])->name('experts.register');
Route::post('/experts/register', [App\Http\Controllers\ExpertRegistrationController::class, 'register'])->name('experts.register.submit');

// Expert Login (OTP)
Route::get('/expert/login', [App\Http\Controllers\Auth\ExpertLoginController::class, 'showForm'])->name('expert.login')->middleware('guest');
Route::post('/expert/login/send-otp', [App\Http\Controllers\Auth\ExpertLoginController::class, 'sendOtp'])->name('expert.login.send-otp')->middleware('guest');
Route::post('/expert/login/verify-otp', [App\Http\Controllers\Auth\ExpertLoginController::class, 'verifyOtp'])->name('expert.login.verify-otp')->middleware('guest');

// ======= إقرار السرية للمحكمين المستقلين =======
Route::prefix('declaration')->group(function () {
    Route::get('/{token}', [ArbitratorDeclarationController::class, 'show'])->name('declaration.show');
    Route::post('/{token}', [ArbitratorDeclarationController::class, 'submit'])->name('declaration.submit');
    Route::get('/{token}/success', [ArbitratorDeclarationController::class, 'success'])->name('declaration.success');
    Route::get('/{token}/download', [ArbitratorDeclarationController::class, 'download'])->name('declaration.download');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Moyasar: صفحة وسيطة يوجَّه إليها المستخدم بعد الدفع (من الويب)
Route::get(
    '/payment/order/{orderId}',
    [PaymentController::class, 'redirect']
)->name('payment.redirect');

// Moyasar: مسار وسيط للتعامل مع نتيجة الدفع وتوجيهها للمسارات القديمة الخاصة بـ Tap
Route::get('/moyasar/callback', function (\Illuminate\Http\Request $request) {
    $status = $request->query('status'); // paid, failed, etc.
    $id = $request->query('id'); // pay_xxx

    if ($status === 'paid') {
        return redirect()->to(url('/payment/callback/package_sucess?id=' . $id . '&status=' . $status));
    } else {
        return redirect()->to(url('/payment/callback/package_error?id=' . $id . '&status=' . $status));
    }
})->name('moyasar.callback');

// Moyasar: صفحة الدفع المستضافة عندنا (مؤمنة برابط مشفر)
Route::get('/moyasar/checkout/{orderId}', function ($orderId) {
    $order = \App\Models\Order::with('user')->findOrFail($orderId);
    $callbackUrl = url("/moyasar/callback");
    return view('payment.moyasar_checkout', compact('order', 'callbackUrl'));
})->name('moyasar.checkout')->middleware('signed');

// Routes قديمة محفوظة للتوافق (يمكن حذفها لاحقاً)
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

        // ======= إقرار السرية والتفعيل =======
        Route::post('/{user}/send-declaration', [UserController::class, 'sendDeclaration'])->name('experts.sendDeclaration');
        Route::post('/{user}/activate', [UserController::class, 'toggleActivate'])->name('experts.activate');
    });
    // Expert Routes
    Route::get('/withdrawals/create', [WithdrawalController::class, 'create'])->name('withdrawals.create');
    Route::post('/withdrawals', [WithdrawalController::class, 'store'])->name('withdrawals.store');
    Route::get('/withdrawals/my', [WithdrawalController::class, 'myWithdrawals'])->name('withdrawals.my');

    // Admin Routes
    Route::get('/withdrawals', [WithdrawalController::class, 'index'])->name('withdrawals.index');
    Route::get('/withdrawals/{id}/show', [WithdrawalController::class, 'show'])->name('withdrawals.show');
    Route::post('/withdrawals/{id}/approve', [WithdrawalController::class, 'approve'])->name('withdrawals.approve');
    Route::post('/withdrawals/{id}/reject', [WithdrawalController::class, 'reject'])->name('withdrawals.reject');

    // Refund Routes
    Route::get('/refunds', [RefundRequestController::class, 'index'])->name('refunds.index');
    Route::get('/refunds/create/{order}', [RefundRequestController::class, 'create'])->name('refunds.create');
    Route::post('/refunds', [RefundRequestController::class, 'store'])->name('refunds.store');
    Route::post('/refunds/{id}/process', [RefundRequestController::class, 'process'])->name('refunds.process');


    Route::post('categories/reorder', [CategoryController::class, 'reorder'])->name('categories.reorder');
    Route::resource('categories', CategoryController::class);
    Route::resource('questions', QuestionController::class);
    Route::post('questions/reorder', [QuestionController::class, 'reorder'])->name('questions.reorder');
    Route::post('questions/toggle-active', [QuestionController::class, 'toggleActive'])->name('questions.toggleActive');
    Route::post('questions/{question}/duplicate', [QuestionController::class, 'duplicate'])->name('questions.duplicate');
    Route::resource('app_pages', AppPageController::class);
    Route::resource('terms', TermConditionController::class);
    Route::resource('question_steps', QuestionStepController::class);
    Route::resource('contacts', ContactController::class);
    Route::prefix('pages')->group(function () {
        Route::get('{type?}', [PageController::class, 'index'])->name('pages.index');
        Route::get('create/{type?}', [PageController::class, 'create'])->name('pages.create');
        Route::post('store', [PageController::class, 'store'])->name('pages.store');
        Route::get('edit/{id}', [PageController::class, 'edit'])->name('pages.edit');
        Route::put('update/{id}', [PageController::class, 'update'])->name('pages.update');
        Route::delete('destroy/{id}', [PageController::class, 'destroy'])->name('pages.destroy');
    });
    Route::resource('faqs', FaqController::class);
    Route::resource('colors', ColorController::class);
    Route::resource('home_steps', HomeStepController::class);
    Route::resource('intros', IntroController::class);
    Route::resource('banners', BannerController::class);



    Route::get('orders', [OrderController::class, 'index'])
        ->name('orders.index');

    Route::get('orders/create', [OrderController::class, 'create'])
        ->name('orders.create');

    Route::get('orders/{order}', [OrderController::class, 'show'])
        ->name('orders.show');

    Route::post('orders/{order}/status', [OrderController::class, 'updateStatus'])
        ->name('orders.updateStatus');

    Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
    Route::post('orders/{order}/evaluate', [OrderController::class, 'expertEvaluate'])->name('orders.expert.evaluate');
    Route::post('/orders/{order}/price', [OrderController::class, 'updatePrice'])
        ->name('orders.updatePrice');

    Route::post('/orders/{order}/ai-evaluate', [OrderController::class, 'aiEvaluate'])
        ->name('orders.ai.evaluate');

    Route::post('/orders/{order}/generate-image', [OrderController::class, 'generateVirtualImage'])
        ->name('orders.generateImage');

    Route::get('payments', [TapPaymentController::class, 'index'])->name('payments.index');
    Route::get('payments/{payment}', [TapPaymentController::class, 'show'])->name('payments.show');
    Route::delete('payments/{payment}', [TapPaymentController::class, 'destroy'])->name('payments.destroy');

    Route::post('/orders/assign-expert', [OrderController::class, 'assignExpert'])->name('orders.assignExpert');

    Route::post(
        '/orders/{order}/thamn-evaluate',
        [OrderController::class, 'thamnEvaluate']
    )->name('orders.thamn.evaluate');

    // WhatsApp Settings
    Route::get('/whatsapp', [\App\Http\Controllers\Admin\WhatsAppController::class, 'index'])->name('admin.whatsapp.index');
    Route::post('/whatsapp/logout', [\App\Http\Controllers\Admin\WhatsAppController::class, 'logout'])->name('admin.whatsapp.logout');

    // Manual Notifications Center
    Route::get('/notifications-center', [\App\Http\Controllers\Admin\NotificationController::class, 'index'])->name('admin.notifications.index');
    Route::post('/notifications-center/send', [\App\Http\Controllers\Admin\NotificationController::class, 'send'])->name('admin.notifications.send');

    // AI Dashboard Route
    Route::post('/ai/ask', [\App\Http\Controllers\Admin\DashboardController::class, 'askAI'])->name('admin.ai.ask');

    // General Settings
    Route::get('/settings', [\App\Http\Controllers\Admin\SettingController::class, 'index'])->name('admin.settings.index');
    Route::post('/settings', [\App\Http\Controllers\Admin\SettingController::class, 'store'])->name('admin.settings.store');

    // AI Valuation Ratings
    Route::resource('ai-valuation-ratings', \App\Http\Controllers\Admin\AiValuationRatingController::class, ['as' => 'admin']);

});

Route::get('lang/{locale}', function ($locale) {
    if (in_array($locale, ['ar', 'en'])) {
        session(['locale' => $locale]);
        app()->setLocale($locale);
    }
    return redirect()->back();
})->name('change.language');

require __DIR__ . '/auth.php';


// =============================================
// DEV ONLY — Email Previews  (local env only)
// Visit: http://localhost:8000/preview/email/{name}
// =============================================
if (app()->environment('local')) {

    Route::get('/preview/email/{name?}', function ($name = 'otp') {
        app()->setLocale('ar');

        // Mock objects for templates that need DB models
        $fakeUser = new stdClass();
        $fakeUser->first_name = 'علاء';
        $fakeUser->last_name  = 'الشافعي';
        $fakeUser->email      = 'alaa@example.com';

        $fakeCategory = new stdClass();
        $fakeCategory->name_ar = 'سيارات فاخرة';
        $fakeCategory->name_en = 'Luxury Cars';

        $fakeOrder = new stdClass();
        $fakeOrder->id               = 2456;
        $fakeOrder->price            = 450;
        $fakeOrder->created_at       = now();
        $fakeOrder->category         = $fakeCategory;
        $fakeOrder->ai_min_price     = 38000;
        $fakeOrder->ai_max_price     = 46000;
        $fakeOrder->ai_price         = 42000;
        $fakeOrder->ai_reasoning     = 'السيارة بحالة ممتازة. الكيلومتراج منخفض نسبياً والمحرك بدون مشاكل. السوق الحالي يدعم هذا السعر.';
        $fakeOrder->user             = $fakeUser;
        $fakeOrder->re_evaluation_count = 0;

        $fakeWithdrawal = new stdClass();
        $fakeWithdrawal->amount     = 1250.00;
        $fakeWithdrawal->iban       = 'SA0380000000608010167519';
        $fakeWithdrawal->created_at = now();
        $fakeWithdrawal->user       = $fakeUser;

        $fakeArbitrator = new stdClass();
        $fakeArbitrator->full_name = 'محمد عبدالله';

        $fakeDeclaration = new stdClass();
        $fakeDeclaration->full_name = 'محمد عبدالله';
        $fakeDeclaration->pdf_path  = null;

        $templates = [
            'otp'                    => ['emails.otp',                    ['otp' => '35716', 'userName' => 'علاء']],
            'reset_password'         => ['emails.reset_password_otp',     ['otp' => '98341', 'userName' => 'علاء']],
            'welcome'                => ['emails.welcome',                 ['user' => $fakeUser, 'title' => 'مرحباً بك']],
            'expert_registration'    => ['emails.expert_registration',     ['user' => $fakeUser, 'password' => 'Secret@123']],
            'valuation_result'       => ['emails.valuation_result',        [
                'order' => $fakeOrder, 'evaluationType' => 'ai',
                'minPrice' => 38000, 'maxPrice' => 46000, 'recommendedPrice' => 42000,
                'reasoning' => $fakeOrder->ai_reasoning, 'categoryName' => 'سيارات فاخرة', 'canReEvaluate' => true,
            ]],
            'admin_withdrawal'       => ['emails.admin_withdrawal',        ['withdrawal' => $fakeWithdrawal]],
            'arbitrator_declaration' => ['emails.arbitrator_declaration',  ['arbitrator' => $fakeArbitrator, 'declarationUrl' => 'https://thmmn.net/sign']],
            'declaration_signed'     => ['emails.declaration_signed',      ['declaration' => $fakeDeclaration, 'downloadUrl' => 'https://thmmn.net/download']],
            'expert_valuation'       => ['emails.expert_valuation',        ['order' => $fakeOrder, 'expert' => $fakeUser]],
            'invoice'                => ['emails.invoice',                 ['order' => $fakeOrder]],
            'system_notification'    => ['emails.system_notification',     ['title' => 'إشعار تجريبي', 'messageBody' => 'هذا إشعار تجريبي من منصة ثمن للتأكد من أن كل شيء يعمل بشكل سليم.', 'actionUrl' => 'https://thmmn.net']],
            'expert_declaration'     => ['emails.expert_declaration',      ['arbitrator' => $fakeArbitrator, 'declarationUrl' => 'https://thmmn.net/sign']],
        ];

        if (!isset($templates[$name])) {
            abort(404, "Email template '{$name}' not found.");
        }

        [$view, $data] = $templates[$name];

        // Prepend the nav bar
        $nav = view('emails._preview_nav', ['name' => $name])->render();
        $email = view($view, $data)->render();
        return $nav . $email;

    })->name('preview.email');
}
