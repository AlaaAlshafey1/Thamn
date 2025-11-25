<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email'      => 'required|email|unique:users,email',
            'phone'      => 'nullable|string|max:255',
            'password'   => 'required|string|min:6|confirmed', // نحتاج password_confirmation
            'image'      => 'nullable|image|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $request->only(['first_name','last_name','email','phone','password']);

        // رفع الصورة إذا موجودة
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('users','public');
        }

        $user = User::create($data);

        // إنشاء token
        $token = $user->createToken('API Token')->plainTextToken;

        return response()->json([
            'status' => true,
            'message' => 'تم التسجيل بنجاح',
            'data' => [
                'user' => $user,
                'token' => $token
            ]
        ]);
    }
}
