<?php

namespace App\Http\Controllers;

use App\Models\WithdrawalRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

        WithdrawalRequest::create([
            'user_id' => $user->id,
            'amount' => $request->amount,
        ]);

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

        return back()->with('success', 'تم رفض طلب السحب');
    }
}
