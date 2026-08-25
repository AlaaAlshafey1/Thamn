<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Mail\ExpertRegistrationMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class ExpertRegistrationController extends Controller
{
    /**
     * Show the expert registration landing page
     */
    public function showForm()
    {
        $categories = \App\Models\Category::where('is_active', true)->get();
        $terms = \App\Models\TermCondition::where('is_active', true)->whereNotNull('file')->orderBy('sort_order')->first();
        return view('public.expert-register', compact('categories', 'terms'));
    }

    /**
     * Preview the agreement as a generated PDF dynamically without filling actual signed details
     */
    public function previewAgreement()
    {
        $dummyDeclaration = new \App\Models\ArbitratorDeclaration([
            'full_name' => 'اسم الخبير (معاينة)',
            'national_id' => 'رقم الهوية',
            'phone' => 'رقم الجوال',
            'email' => 'البريد الإلكتروني',
            'signature' => null,
            'signed_at' => now(),
        ]);

        $pdf = \Mccarlosen\LaravelMpdf\Facades\LaravelMpdf::loadView('pdf.declaration', [
            'declaration' => $dummyDeclaration,
            'user' => null,
        ]);

        return $pdf->stream('preview_agreement.pdf');
    }

    /**
     * Handle expert registration form submission
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|max:20',
            'bank_name' => 'nullable|string|max:255',
            'iban' => 'nullable|string|max:34',
            'experience' => 'nullable|string',
            'expertise' => 'nullable|string',
            'certificates' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'experience_certificate' => 'nullable|file|mimes:pdf,jpeg,png,jpg|max:5120',
        ], [
            'first_name.required' => 'الاسم الأول مطلوب',
            'last_name.required' => 'اسم العائلة مطلوب',
            'email.required' => 'البريد الإلكتروني مطلوب',
            'email.email' => 'يرجى إدخال بريد إلكتروني صحيح',
            'email.unique' => 'البريد الإلكتروني مُسجل مسبقاً',
            'phone.required' => 'رقم الجوال مطلوب',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $plainPassword = \Illuminate\Support\Str::random(10);
        $data = $request->except(['image', 'experience_certificate']);
        $data['password'] = Hash::make($plainPassword);
        $data['is_active'] = false; // Pending approval

        // Assign role_id if expert role exists
        $expertRole = \App\Models\Role::where('name', 'expert')->first();
        if ($expertRole) {
            $data['role_id'] = $expertRole->id;
        }

        \Illuminate\Support\Facades\DB::beginTransaction();
        $uploadedFiles = []; // لتتبع الملفات لحذفها لو صار خطأ

        // Handle Image Upload
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $name = time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/users'), $name);
            $data['image'] = $name;
            $uploadedFiles[] = public_path('uploads/users/' . $name);
        }

        // Handle Certificate Upload
        if ($request->hasFile('experience_certificate')) {
            $file = $request->file('experience_certificate');
            $name = time() . '_cert.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/experts/certificates'), $name);
            $data['experience_certificate'] = $name;
            $uploadedFiles[] = public_path('uploads/experts/certificates/' . $name);
        }

        try {
            $user = User::create($data);

            // Assign expert role (Spatie)
            try {
                if (\Spatie\Permission\Models\Role::where('name', 'expert')->exists()) {
                    $user->assignRole('expert');
                }
            } catch (\Exception $e) {
                // Ignore if role doesn't exist to prevent crash
                \Log::warning('Role expert not found for assignment');
            }

            // 1. ========================================
            // إنشاء وتوليد الاتفاقية القانونية وحفظ الـ PDF
            // ========================================
            try {
                $token = \Illuminate\Support\Str::random(64);
                $declaration = \App\Models\ArbitratorDeclaration::create([
                    'user_id' => $user->id,
                    'token' => $token,
                    'full_name' => $user->first_name . ' ' . $user->last_name,
                    'national_id' => 'من التسجيل',
                    'phone' => $user->phone,
                    'email' => $user->email,
                    'expertise' => $user->expertise,
                    'signature' => null,
                    'signed_at' => now(),
                ]);

                // توليد الـ PDF
                $pdf = \Mccarlosen\LaravelMpdf\Facades\LaravelMpdf::loadView('pdf.declaration', [
                    'declaration' => $declaration,
                    'user' => $user,
                ]);

                // حفظ PDF في storage
                $pdfPath = 'declarations/' . $token . '.pdf';
                \Illuminate\Support\Facades\Storage::disk('public')->put($pdfPath, $pdf->output());

                // تحديث مسار الـ PDF
                $declaration->update(['pdf_path' => $pdfPath]);
                $downloadUrl = route('declaration.download', ['token' => $token]);

                // 2. إرسال الإيميل مع مرفق PDF
                Mail::to($user->email)->send(
                    new \App\Mail\DeclarationSignedMail($declaration, $downloadUrl)
                );

                // إرسال واتساب للخبير بالإتفاقية
                $whatsapp = app(\App\Services\WhatsAppService::class);
                if ($user->phone) {
                    $whatsapp->sendMessage(
                        $user->phone,
                        "مرحباً بك {$user->first_name} 👋\nشكراً لتسجيلك كخبير في منصة ثمن. لقد استلمنا طلبك بنجاح.\n\n✅ تم تسجيل موافقتك على الاتفاقية القانونية للتعاون وإرسال نسخة PDF إلى بريدك الإلكتروني ({$user->email}).\nيمكنك تحميلها من الرابط:\n{$downloadUrl}\n\nفريقنا سيراجع بياناتك ونرد عليك في أقرب وقت. نتمنى لك يوماً سعيداً!"
                    );
                }
            } catch (\Exception $e) {
                \Log::error('Expert Auto-Declaration Failed: ' . $e->getMessage());
                // Throw to trigger rollback only if you consider PDF failure a catastrophic failure
                // throw $e; 
            }

            // 3. ========================================
            // تنبيه الإدارة (Admins) بتسجيل خبير جديد
            // ========================================
            try {
                $admins = User::role('superadmin')->get();
                $msg = \App\Services\WhatsAppService::getTemplate('new_expert_reg', ['name' => $user->first_name . ' ' . $user->last_name]);
                $whatsapp = app(\App\Services\WhatsAppService::class);

                $adminEmail = config('mail.admin_email', 'thmmnapplic@gmail.com');
                Mail::to($adminEmail)->send(new \App\Mail\SystemNotificationMail(
                    'يا مدير، خبير جديد سجل ووافق على الاتفاقية!',
                    "فيه خبير جديد سجل بالمنصة باسم: " . $user->first_name . " " . $user->last_name . ".\n\nالخبير وافق ضمينًا على الاتفاقية القانونية وتم توليد ملف PDF في ملفه الشخصي.\nادخل على لوحة التحكم وشيك على ملفه.",
                    route('experts.show', $user->id)
                ));

                foreach ($admins as $admin) {
                    if ($admin->phone) {
                        $whatsapp->sendMessage($admin->phone, $msg);
                    }
                }
            } catch (\Exception $e) {
                \Log::error('Expert Registration Admin Notification Failed: ' . $e->getMessage());
            }

            \Illuminate\Support\Facades\DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'تم تسجيل طلبك بنجاح! وتم إرسال نسخة من الاتفاقية القانونية إلى إيميلك.',
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();

            // حذف الملفات التي تم رفعها قبل حدوث الخطأ
            foreach ($uploadedFiles as $file) {
                if (\Illuminate\Support\Facades\File::exists($file)) {
                    \Illuminate\Support\Facades\File::delete($file);
                }
            }

            \Log::error('Expert Registration Failed: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'حدث خطأ أثناء حفظ البيانات، يرجى المحاولة لاحقاً. ' . $e->getMessage(),
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
