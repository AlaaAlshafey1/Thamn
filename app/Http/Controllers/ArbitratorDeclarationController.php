<?php

namespace App\Http\Controllers;

use App\Mail\ArbitratorDeclarationMail;
use App\Models\ArbitratorDeclaration;
use App\Models\User;
use Mccarlosen\LaravelMpdf\Facades\LaravelMpdf as Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ArbitratorDeclarationController extends Controller
{
    /**
     * إرسال رابط الإقرار للمحكم (API)
     */
    public function sendLink(Request $request, $userId)
    {
        $user = User::findOrFail($userId);

        // إنشاء أو تحديث إقرار المحكم
        $declaration = ArbitratorDeclaration::firstOrCreate(
            ['user_id' => $user->id],
            ['token' => Str::random(64)]
        );

        // لو موجود بالفعل وما اتوقعش، نبقي نفس الرابط
        $url = route('declaration.show', ['token' => $declaration->token]);

        try {
            Mail::to($user->email)->send(new ArbitratorDeclarationMail($user, $url));
        } catch (\Exception $e) {
            \Log::error('ArbitratorDeclarationMail failed: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'فشل إرسال الإيميل: ' . $e->getMessage()
            ], 500);
        }

        return response()->json([
            'status' => true,
            'message' => 'تم إرسال رابط الإقرار بنجاح إلى ' . $user->email,
            'declaration_url' => $url,
            'is_signed' => $declaration->isSigned(),
        ]);
    }

    /**
     * عرض صفحة الإقرار
     */
    public function show(string $token)
    {
        $declaration = ArbitratorDeclaration::where('token', $token)->firstOrFail();
        $user = $declaration->user;

        return view('declaration.show', compact('declaration', 'user', 'token'));
    }

    /**
     * حفظ البيانات والتوقيع وتوليد PDF
     */
    public function submit(Request $request, string $token)
    {
        $declaration = ArbitratorDeclaration::where('token', $token)->firstOrFail();

        $request->validate([
            'full_name' => 'required|string|max:255',
            'national_id' => 'required|string|max:50',
            'phone' => 'required|string|max:20',
            'email' => 'required|email',
            'signature' => 'required|string', // base64
            'nationality' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'expertise' => 'nullable|string|max:255',
        ], [
            'full_name.required' => 'الاسم الكامل مطلوب',
            'national_id.required' => 'رقم الهوية مطلوب',
            'phone.required' => 'رقم الجوال مطلوب',
            'email.required' => 'البريد الإلكتروني مطلوب',
            'email.email' => 'البريد الإلكتروني غير صحيح',
            'signature.required' => 'التوقيع مطلوب',
        ]);

        // تحديث بيانات الإقرار
        $declaration->update([
            'full_name' => $request->full_name,
            'national_id' => $request->national_id,
            'phone' => $request->phone,
            'email' => $request->email,
            'nationality' => $request->nationality,
            'city' => $request->city,
            'expertise' => $request->expertise,
            'signature' => $request->signature,
            'signed_at' => now(),
        ]);

        // توليد PDF
        $pdf = Pdf::loadView('pdf.declaration', [
            'declaration' => $declaration,
            'user' => $declaration->user,
        ]);

        // حفظ PDF في storage
        $pdfPath = 'declarations/' . $token . '.pdf';
        Storage::disk('public')->put($pdfPath, $pdf->output());

        $declaration->update(['pdf_path' => $pdfPath]);

        // إرسال إيميل للمشرفين بأن الخبير وقع
        try {
            $admins = User::role('superadmin')->get();
            $adminEmail = config('mail.admin_email', 'alaa.alshafey12345@gmail.com');
            $expertName = $declaration->full_name ?? ($declaration->user->first_name . ' ' . $declaration->user->last_name);
            $viewUrl = route('experts.show', $declaration->user_id);

            Mail::to($adminEmail)->send(new \App\Mail\SystemNotificationMail(
                '📋 خبير وقّع على وثيقة الإقرار',
                "قام الخبير **{$expertName}** بالتوقيع على وثيقة الشروط والأحكام وإقرار السرية بتاريخ " . now()->format('d/m/Y H:i') . ".\n\nيمكنك الآن مراجعة الوثيقة وتفعيل الخبير من لوحة التحكم.",
                $viewUrl
            ));
        } catch (\Exception $e) {
            \Log::error('Admin Declaration Notification Failed: ' . $e->getMessage());
        }

        return redirect()->route('declaration.success', ['token' => $token]);
    }

    /**
     * صفحة نجاح الإقرار
     */
    public function success(string $token)
    {
        $declaration = ArbitratorDeclaration::where('token', $token)->firstOrFail();

        if (!$declaration->isSigned()) {
            return redirect()->route('declaration.show', ['token' => $token]);
        }

        return view('declaration.success', compact('declaration', 'token'));
    }

    /**
     * تحميل PDF الإقرار
     */
    public function download(string $token)
    {
        $declaration = ArbitratorDeclaration::where('token', $token)->firstOrFail();

        if (!$declaration->isSigned() || !$declaration->pdf_path) {
            abort(404, 'الإقرار لم يُكتمل بعد');
        }

        if (!Storage::disk('public')->exists($declaration->pdf_path)) {
            abort(404, 'الملف غير موجود');
        }

        return Storage::disk('public')->download(
            $declaration->pdf_path,
            'إقرار_السرية_' . $declaration->full_name . '.pdf'
        );
    }
}
