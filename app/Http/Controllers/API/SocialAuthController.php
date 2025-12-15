<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\UserResource;

class SocialAuthController extends Controller
{



    public function checkSocialAccount(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'provider'  => 'required|in:google',
            'social_id' => 'required|string',
            'email'     => 'nullable|email'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // بحث عن الحساب
        $user = User::where('social_id', $request->social_id)
                    ->where('social_provider', $request->provider)
                    ->first();

        if (!$user) {
            return response()->json([
                'status' => true,
                'is_registered' => false,
                'is_completed' => false
            ]);
        }

        return response()->json([
            'status' => true,
            'is_registered' => true,
            'is_completed' => true,
            'data' => new UserResource($user)
        ]);
    }




    public function registerSocialAccount(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'provider'  => 'required|in:google',
            'social_id' => 'required|string',
            'email'     => 'required|email',
            'name'      => 'required|string|max:255',
            'image'     => 'nullable|image|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::where('email', $request->email)->first();

        if ($user) {
            $user->update([
                'social_id'       => $request->social_id,
                'social_provider' => $request->provider,
            ]);
        } else {
            $fullName = explode(" ", $request->name);
            $first = $fullName[0] ?? '';
            $last  = $fullName[1] ?? '';

            $data = [
                'first_name'      => $first,
                'last_name'       => $last,
                'email'           => $request->email,
                'password'        => Hash::make(uniqid()),
                'phone'           => $request->phone ??null,
                'social_id'       => $request->social_id,
                'social_provider' => $request->provider,
                'is_verified' => "1",
            ];

            if ($request->hasFile('image')) {
                $data['image'] = $request->file('image')->store('users', 'public');
            }

            $user = User::create($data);
        }

        $token = $user->createToken('API Token')->plainTextToken;

        return response()->json([
            'status'  => true,
            'message' => 'Social Login successful',
            'data' => [
                'user'  => new UserResource($user),
                'token' => $token
            ]
        ]);
    }
}
