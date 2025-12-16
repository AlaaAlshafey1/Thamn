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

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email'      => 'required|email|unique:users,email',
            'phone'      => 'required|string|max:255|unique:users,phone',
            'password'   => 'required|string|min:6|confirmed',
            'image'      => 'nullable|image|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $request->only(['first_name','last_name','email','phone']);
        $data['password'] = Hash::make($request->password);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('users','public');
        }

        $user = User::create($data);

        // generate OTP
        $otp = rand(1000, 9999);

        \DB::table('otps')->insert([
            'user_id' => $user->id,
            'otp' => $otp,
            'expires_at' => now()->addMinutes(5),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // هنا تبعته SMS لو عندك خدمة

        return response()->json([
            'status' => true,
            'message' => 'تم إنشاء الحساب - يرجى إدخال رمز التفعيل',
            'data' => [
                'user_id' => $user->id,
                'otp' => $otp, // يمكنك إزالته فى الإنتاج
            ]
        ]);
    }

    public function checkPhone(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string'
        ], [
            'phone.required' => lang('رقم الهاتف مطلوب', 'Phone is required', $request),
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::where('phone', $request->phone)->where("is_verified","1")->first();

        if (! $user) {
            return response()->json([
                'status' => true,
                'exists' => false,
                'message' => lang('الرقم غير مسجل', 'Phone not registered', $request),
            ]);
        }

        return response()->json([
            'status' => true,
            'exists' => true,
            'is_verified' => (bool) $user->is_verified,
            'message' => $user->is_verified
                ? lang('الرقم مسجل ومفعل', 'Phone exists and verified', $request)
                : lang('الرقم مسجل لكنه غير مفعل', 'Phone exists but not verified', $request),
        ]);
    }


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

        $user = User::Where('phone', $request->phone)->where("is_verified","1")
                    ->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => false,
                'message' => lang('بيانات الدخول غير صحيحة', 'Invalid login credentials', $request)
            ], 401);
        }

        if (!$user->is_verified) {
            return response()->json([
                'status' => false,
                'message' => 'الحساب غير مفعل - يرجى إدخال OTP'
            ], 403);
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


    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => true,
            'message' => lang('تم تسجيل الخروج بنجاح', 'Logout successful', $request)
        ]);
    }

    public function verifyOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'otp' => 'required|digits:4',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
        }

        $otp = \DB::table('otps')
            ->where('user_id', $request->user_id)
            ->where('otp', $request->otp)
            ->where('is_used', false)
            ->where('expires_at', '>=', now())
            ->first();

        if (!$otp) {
            return response()->json([
                'status' => false,
                'message' => transMsg('otp_invalid_or_expired', $request)
            ], 400);
        }

        // mark otp used
        \DB::table('otps')->where('id', $otp->id)->update(['is_used' => true]);

        User::where('id', $request->user_id)->update(['is_verified' => true]);

        $user = User::find($request->user_id);
        $token = $user->createToken('API Token')->plainTextToken;

        return response()->json([
            'status' => true,
            'message' => transMsg('account_verified_successfully', $request),
            'data' => [
                'user' => new UserResource($user),
                'token' => $token
            ]
        ]);
    }

    public function resendOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
        }

        $otp = rand(1000, 9999);

        \DB::table('otps')->insert([
            'user_id' => $request->user_id,
            'otp' => $otp,
            'expires_at' => now()->addMinutes(5),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json([
            'status' => true,
            'message' => transMsg('otp_sent_successfully', $request),
            'otp' => $otp
        ]);
    }

    public function profile(Request $request)
    {
        return response()->json([
            'status' => true,
            'message' => transMsg('profile_fetched_successfully', $request),
            'data' => new UserResource($request->user())
        ]);
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'first_name' => 'nullable|string|max:255',
            'last_name'  => 'nullable|string|max:255',
            'email'      => 'nullable|email|unique:users,email,' . $user->id,
            'phone'      => 'nullable|string|max:255|unique:users,phone,' . $user->id,
            'password'   => 'nullable|string|min:6|confirmed',
            'image'      => 'nullable|image|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        if ($request->filled('first_name')) $user->first_name = $request->first_name;
        if ($request->filled('last_name'))  $user->last_name = $request->last_name;
        if ($request->filled('email'))      $user->email = $request->email;
        if ($request->filled('phone'))      $user->phone = $request->phone;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        if ($request->hasFile('image')) {
            if ($user->image && \Storage::disk('public')->exists($user->image)) {
                \Storage::disk('public')->delete($user->image);
            }

            $user->image = $request->file('image')->store('users', 'public');
        }

        $user->save();

        return response()->json([
            'status' => true,
            'message' => transMsg('profile_updated_successfully', $request),
            'data' => new UserResource($user)
        ]);
    }


}
