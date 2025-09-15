<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Mail\OtpMail;
use Carbon\Carbon;

class AuthController extends Controller
{
    public function requestOtpLogin(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            $user = User::create([
                'name' => strstr($request->email, '@', true),
                'email' => $request->email,
                'password' => bcrypt(str()->random(10))
            ]);
        }
        // Generate OTP
        $otp = rand(100000, 999999);
        $user->otp = $otp;
        $user->otp_expires_at = Carbon::now()->addMinutes(10);
        $user->save();
        // Send OTP via Email
        // Mail::raw("Your OTP is: $otp (valid for 10 minutes)", function ($message) use ($user) {
        //     $message->to($user->email)
        //         ->subject('Your Login OTP');
        // });
        // Send email
        $userName = strstr($request->email, '@', true);
        Mail::to($request->email)->send(new OtpMail($otp, $userName));
        return response()->json([
            'status' => 200,
            'message' => 'OTP sent to your email'
        ]);
    }

    /**
     *  Verify OTP & get token
     */
    public function verifyOtpLogin(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp'   => 'required|digits:6'
        ]);

        $user = User::where('email', $request->email)
            ->where('otp', $request->otp)
            ->first();

        if (!$user) {
            return response()->json([
                'status' => 401,
                'message' => 'Invalid OTP'
            ], 401);
        }

        if (Carbon::now()->greaterThan($user->otp_expires_at)) {
            return response()->json([
                'status' => 410,
                'message' => 'OTP expired'
            ], 410);
        }
        $user->otp = null;
        $user->otp_expires_at = null;
        $user->save();
        $token = $user->createToken('API Token')->plainTextToken;
        return response()->json([
            'status' => 200,
            'message' => 'Login successful',
            'token' => $token
        ]);
    }

    /**
     * Resend OTP
     */
    public function resendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'status' => 404,
                'message' => 'User not found'
            ], 404);
        }
        $otp = rand(100000, 999999);

        // Save to DB (valid for 10 minutes)
        $user->otp = $otp;
        $user->otp_expires_at = Carbon::now()->addMinutes(10);
        $user->save();

        // Send OTP via Email
        Mail::raw("Your new OTP is: $otp (valid for 10 minutes)", function ($message) use ($user) {
            $message->to($user->email)
                ->subject('Resend OTP');
        });

        return response()->json([
            'status' => 200,
            'message' => 'OTP resent to your email'
        ]);
    }


    // Logout
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'status' => 200,
            'message' => 'Logged out successfully'
        ]);
    }
}
