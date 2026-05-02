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
        return view('public.expert-register', compact('categories'));
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

        // Handle Image Upload
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $name = time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/users'), $name);
            $data['image'] = $name;
        }

        // Handle Certificate Upload
        if ($request->hasFile('experience_certificate')) {
            $file = $request->file('experience_certificate');
            $name = time() . '_cert.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/experts/certificates'), $name);
            $data['experience_certificate'] = $name;
        }

        try {
            $user = User::create($data);

            // Assign expert role (Spatie)
            $user->assignRole('expert');

            // Send welcome email with password
            try {
                Mail::to($user->email)->send(new ExpertRegistrationMail($user, $plainPassword));
                
                // Notify All SuperAdmins via WhatsApp & Email
                $whatsapp = app(\App\Services\WhatsAppService::class);
                $admins = User::role('superadmin')->get();
                
                $msg = \App\Services\WhatsAppService::getTemplate('new_expert_reg', ['name' => $user->first_name . ' ' . $user->last_name]);

                $adminEmail = 'alaa.alshafey12345@gmail.com';
                Mail::to($adminEmail)->send(new \App\Mail\SystemNotificationMail(
                    'يا مدير، خبير جديد يبي ينضم لثمن!',
                    "فيه خبير جديد سجل بالمنصة باسم: " . $user->first_name . " " . $user->last_name . ".\nادخل على لوحة التحكم وشيك على ملفه.",
                    route('experts.show', $user->id)
                ));

                foreach ($admins as $admin) {
                    // 1. WhatsApp
                    if ($admin->phone) {
                        $whatsapp->sendMessage($admin->phone, $msg);
                    }
                }
                
            } catch (\Exception $e) {
                \Log::error('Expert Registration Notification Failed: ' . $e->getMessage());
            }

            return response()->json([
                'status' => true,
                'message' => 'تم تسجيل طلبك بنجاح! سنراجع بياناتك ونرد عليك في أقرب وقت.',
            ]);
        } catch (\Exception $e) {
            \Log::error('Expert Registration Failed: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'حدث خطأ أثناء حفظ البيانات، يرجى المحاولة لاحقاً.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
