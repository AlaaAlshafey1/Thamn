<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\UserResource;

class AuthController extends Controller
{
    /**
     * تسجيل مستخدم جديد
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email'      => 'required|email|unique:users,email',
            'phone'      => 'required|string|max:255',
            'password' => 'required|string|min:6|confirmed',
            'image'      => 'nullable|image|max:2048',
        ], [
            'first_name.required' => lang('الاسم الأول مطلوب', 'First name is required', $request),
            'last_name.required'  => lang('اسم العائلة مطلوب', 'Last name is required', $request),
            'email.required'      => lang('البريد الإلكتروني مطلوب', 'Email is required', $request),
            'email.email'         => lang('صيغة البريد الإلكتروني غير صحيحة', 'Email format is invalid', $request),
            'email.unique'        => lang('البريد الإلكتروني مستخدم بالفعل', 'Email already taken', $request),
            'phone.required'      => lang('رقم الهاتف مطلوب', 'Phone is required', $request),
            'password.required'   => lang('كلمة المرور مطلوبة', 'Password is required', $request),
            'password.min'        => lang('كلمة المرور يجب أن تكون على الأقل 6 أحرف', 'Password must be at least 6 characters', $request),
            'password.confirmed'  => lang('تأكيد كلمة المرور لا يطابق', 'Password confirmation does not match', $request),
            'image.image'         => lang('الملف المرفوع ليس صورة', 'The uploaded file must be an image', $request),
            'image.max'           => lang('حجم الصورة يجب ألا يزيد عن 2MB', 'Image size must not exceed 2MB', $request),
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $request->only(['first_name','last_name','email','phone']);
        $data['password'] = Hash::make($request->password);

        // رفع الصورة إذا موجودة
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('users','public');
        }

        $user = User::create($data);

        $token = $user->createToken('API Token')->plainTextToken;

        return response()->json([
            'status' => true,
            'message' => lang('تم التسجيل بنجاح', 'Registration successful', $request),
            'data' => [
                'user'  => new UserResource($user),
                'token' => $token
            ]
        ]);
    }

    /**
     * تسجيل الدخول بواسطة الهاتف أو البريد
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string',
            'password'       => 'required',
        ], [
            'phone.required' => lang(' رقم الهاتف مطلوب', ' phone is required', $request),
            'password.required'       => lang('كلمة المرور مطلوبة', 'Password is required', $request),
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // البحث في البريد أو الهاتف
        $user = User::Where('phone', $request->phone)
                    ->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => false,
                'message' => lang('بيانات الدخول غير صحيحة', 'Invalid login credentials', $request)
            ], 401);
        }

        $token = $user->createToken('API Token')->plainTextToken;

        return response()->json([
            'status' => true,
            'message' => lang('تم تسجيل الدخول بنجاح', 'Login successful', $request),
            'data' => [
                'user'  => new UserResource($user),
                'token' => $token
            ]
        ]);
    }

    /**
     * تسجيل الخروج
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => true,
            'message' => lang('تم تسجيل الخروج بنجاح', 'Logout successful', $request)
        ]);
    }
}
