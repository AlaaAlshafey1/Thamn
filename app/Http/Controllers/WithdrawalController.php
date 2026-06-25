<?php

namespace App\Http\Controllers;

use App\Models\WithdrawalRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class WithdrawalController extends Controller
{
    // عرض كل طلبات السحب (للادمن)
    public function index()
    {
        $requests = WithdrawalRequest::with('user')->latest()->get();
        return view('withdrawals.index', compact('requests'));
    }

    public function myWithdrawals()
    {
        $user = auth()->user();
        $requests = WithdrawalRequest::where('user_id', $user->id)->latest()->get();
        return view('withdrawals.my', compact('requests'));
    }

    // عرض تفاصيل طلب سحب محدد (للادمن)
    public function show($id)
    {
        $request = WithdrawalRequest::with('user')->findOrFail($id);
        $expert = $request->user;

        // الحصول على جميع الطلبات التي قيمها هذا الخبير
        $evaluatedOrders = \App\Models\Order::where('expert_id', $expert->id)
            ->whereIn('status', ['estimated', 'evaluated', 'finished', 'completed'])
            ->with(['user', 'details'])
            ->latest()
            ->get();

        // الحصول على طلبات السحب السابقة لنفس الخبير (استبعاد الطلب الحالي)
        $previousWithdrawals = WithdrawalRequest::where('user_id', $expert->id)
            ->where('id', '!=', $id)
            ->latest()
            ->get();

        return view('withdrawals.show', compact('request', 'expert', 'evaluatedOrders', 'previousWithdrawals'));
    }


    // صفحة إنشاء طلب سحب (للخبير)
    public function create()
    {
        return view('withdrawals.create');
    }

    // حفظ طلب السحب
    public function store(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1'
        ]);

        $user = Auth::user();

        // التأكد من الرصيد
        if ($request->amount > $user->balance) {
            return back()->with('error', 'الرصيد غير كافي');
        }

        $withdrawal = WithdrawalRequest::create([
            'user_id' => $user->id,
            'amount' => $request->amount,
            'notes' => $request->notes,
            'method' => $request->method ?? 'bank',
        ]);

        // Notify All SuperAdmins via WhatsApp, Email, and Database
        try {
            $whatsapp = app(\App\Services\WhatsAppService::class);
            $admins = User::role('superadmin')->get();

            $msg = \App\Services\WhatsAppService::getTemplate('new_withdrawal', [
                'name' => $user->first_name . ' ' . $user->last_name,
                'amount' => $request->amount
            ]);

            $adminEmail = 'thmmnapplic@gmail.com';
            // Send the beautiful formatted table email
            Mail::to($adminEmail)->send(new \App\Mail\AdminWithdrawalRequestedMail($withdrawal));

            foreach ($admins as $admin) {
                if ($admin->phone) {
                    $whatsapp->sendMessage($admin->phone, $msg);
                }
            }
        } catch (\Exception $e) {
            \Log::error('Withdrawal Notifications Failed: ' . $e->getMessage());
        }

        return redirect()->route('withdrawals.create')->with('success', 'تم إرسال طلب السحب بنجاح وسيتم معالجته قريباً.');
    }

    // الموافقة على السحب
    public function approve(Request $request, $id)
    {
        $req = WithdrawalRequest::findOrFail($id);
        $transferType = $request->input('transfer_type', 'manual');

        if ($req->status != 'pending') {
            return back()->with('error', 'الطلب تم التعامل معه بالفعل');
        }

        $user = $req->user;

        if ($req->amount > $user->balance) {
            return back()->with('error', 'الرصيد غير كافي');
        }

        // ==========================================
        // Tap Payments Automated Transfer Integration
        // ==========================================
        if ($transferType === 'auto') {
            try {
                $tapSecretKey = config('services.tap.secret_key');
                if ($tapSecretKey) {
                    // Prepare Transfer payload
                    $transferPayload = [
                        "amount" => (float) $req->amount,
                        "currency" => "SAR",
                        "destination" => [
                            "type" => "bank_account",
                            "bank_account" => [
                                "iban" => $user->iban,
                                "account_name" => $user->first_name . ' ' . $user->last_name,
                                "bank_name" => $user->bank_name
                            ]
                        ],
                        "metadata" => [
                            "withdrawal_id" => $req->id,
                            "expert_id" => $user->id
                        ],
                        "description" => "سحب رصيد الخبير " . $user->first_name . ' من منصة ثمن'
                    ];

                    // Execute the POST request to Tap Transfers API
                    $response = \Illuminate\Support\Facades\Http::withToken($tapSecretKey)
                        ->post('https://api.tap.company/v2/transfers', $transferPayload);

                    if (!$response->successful()) {
                        \Log::error('Tap Transfer Failed: ' . $response->body());
                        // We gracefully handle if Tap Transfer API is not yet activated for the merchant or if IBAN is invalid
                        return back()->with('error', 'فشل التحويل الآلي عبر Tap Payments: تأكد من تفعيل خدمة (Transfers) وأن رقم الـ IBAN للخبير صحيح.');
                    }
                } else {
                    return back()->with('error', 'مفتاح الربط مع Tap Payments غير متوفر.');
                }
            } catch (\Exception $e) {
                \Log::error('Tap Transfer Exception: ' . $e->getMessage());
                return back()->with('error', 'حدث خطأ غير متوقع أثناء محاولة الاتصال ببوابة الدفع.');
            }
        }

        // خصم الرصيد
        $user->balance -= $req->amount;
        $user->save();

        // تحديث حالة الطلب
        $req->status = 'approved';
        $req->save();

        // Notify Expert via WhatsApp and Email
        try {
            if ($user->phone) {
                $whatsapp = app(\App\Services\WhatsAppService::class);
                $msg = \App\Services\WhatsAppService::getTemplate('withdrawal_approved', ['amount' => $req->amount]);
                $whatsapp->sendMessage($user->phone, $msg);
            }
            if ($user->email) {
                Mail::to($user->email)->send(new \App\Mail\SystemNotificationMail(
                    'تم قبول طلب السحب 💸',
                    "أهلاً {$user->first_name}، لقد تمت الموافقة على طلب السحب الخاص بك بمبلغ " . number_format($req->amount, 2) . " ريال وسيتم تحويله لحسابك البنكي قريباً عبر بوابة الدفع.",
                    route('withdrawals.my')
                ));
            }
        } catch (\Exception $e) {
            \Log::error('Withdrawal Approved Notifications Failed: ' . $e->getMessage());
        }

        return back()->with('success', 'تم الموافقة على طلب السحب وتحويل الأموال آلياً بنجاح.');
    }

    // رفض السحب
    public function reject($id)
    {
        $req = WithdrawalRequest::findOrFail($id);

        if ($req->status != 'pending') {
            return back()->with('error', 'الطلب تم التعامل معه بالفعل');
        }

        $req->status = 'rejected';
        $req->save();

        // Notify Expert via WhatsApp and Email
        try {
            if ($req->user->phone) {
                $whatsapp = app(\App\Services\WhatsAppService::class);
                $msg = \App\Services\WhatsAppService::getTemplate('withdrawal_rejected');
                $whatsapp->sendMessage($req->user->phone, $msg);
            }
            if ($req->user->email) {
                Mail::to($req->user->email)->send(new \App\Mail\SystemNotificationMail(
                    'عذراً، تم رفض طلب السحب ❌',
                    "أهلاً {$req->user->first_name}، نعتذر منك، لقد تم رفض طلب السحب الأخير الخاص بك. يرجى التواصل مع الإدارة لمزيد من التفاصيل.",
                    route('withdrawals.my')
                ));
            }
        } catch (\Exception $e) {
            \Log::error('Withdrawal Rejected Notifications Failed: ' . $e->getMessage());
        }

        return back()->with('success', 'تم رفض طلب السحب');
    }
}
