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
        ]);

        // Notify All SuperAdmins via WhatsApp, Email, and Database
        try {
            $whatsapp = app(\App\Services\WhatsAppService::class);
            $admins = User::role('superadmin')->get();
            
            $msg = \App\Services\WhatsAppService::getTemplate('new_withdrawal', [
                'name' => $user->first_name . ' ' . $user->last_name,
                'amount' => $request->amount
            ]);

            $adminEmail = 'alaa.alshafey12345@gmail.com';
            Mail::to($adminEmail)->send(new \App\Mail\SystemNotificationMail(
                'يا مدير، فيه طلب سحب أرباح جديد!',
                "الخبير {$user->first_name} طلب سحب مبلغ: " . number_format($request->amount, 2) . " ريال.\nتكفى لا تبطي عليه وراجع الطلب الحين.",
                route('withdrawals.index')
            ));

            foreach ($admins as $admin) {
                // 1. Notify via WhatsApp (if phone exists)
                if ($admin->phone) {
                    $whatsapp->sendMessage($admin->phone, $msg);
                }

                // 2. Database Notification (Optional - if you have the class)
                // $admin->notify(new \App\Notifications\NewWithdrawalNotification($withdrawal));
            }
        } catch (\Exception $e) {
            \Log::error('Withdrawal Notifications Failed: ' . $e->getMessage());
        }

        return redirect()->route('withdrawals.create')->with('success', 'تم إرسال طلب السحب بنجاح');
    }

    // الموافقة على السحب
    public function approve($id)
    {
        $req = WithdrawalRequest::findOrFail($id);

        if ($req->status != 'pending') {
            return back()->with('error', 'الطلب تم التعامل معه بالفعل');
        }

        $user = $req->user;

        if ($req->amount > $user->balance) {
            return back()->with('error', 'الرصيد غير كافي');
        }

        // خصم الرصيد
        $user->balance -= $req->amount;
        $user->save();

        // تحديث حالة الطلب
        $req->status = 'approved';
        $req->save();

        // Notify Expert via WhatsApp
        try {
            if ($user->phone) {
                $whatsapp = app(\App\Services\WhatsAppService::class);
                $msg = \App\Services\WhatsAppService::getTemplate('withdrawal_approved', ['amount' => $req->amount]);
                $whatsapp->sendMessage($user->phone, $msg);
            }
        } catch (\Exception $e) {
            \Log::error('Withdrawal Approved WhatsApp Failed: ' . $e->getMessage());
        }

        return back()->with('success', 'تم الموافقة على طلب السحب');
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

        // Notify Expert via WhatsApp
        try {
            if ($req->user->phone) {
                $whatsapp = app(\App\Services\WhatsAppService::class);
                $msg = \App\Services\WhatsAppService::getTemplate('withdrawal_rejected');
                $whatsapp->sendMessage($req->user->phone, $msg);
            }
        } catch (\Exception $e) {
            \Log::error('Withdrawal Rejected WhatsApp Failed: ' . $e->getMessage());
        }

        return back()->with('success', 'تم رفض طلب السحب');
    }
}
