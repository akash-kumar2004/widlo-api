<?php

namespace App\Http\Controllers\otp;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\OtpMail;


class GenrateotpController extends Controller
{
    public function sendotp(request $request)
    {
        $validator = Validator::make($request->all(), [
            'btn_typ' => 'required',
            'phone_no' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ], 401);
        }
        if ($request->btn_typ == 'send_otp') {
            $check = DB::table('students')->where('contact_number', $request->phone_no)->orwhere('alternate_contact_number', $request->phone_no)->exists();
            if ($check) {
                $otp = '1234';
                $otp_expire = Carbon::now()->addMinute(5);
                $save_otp = DB::table('students')->where('contact_number', $request->phone_no)->orwhere('alternate_contact_number', $request->phone_no)->update([
                    'otp' => $otp,
                    'otp_expire' => $otp_expire
                ]);

                return response()->json([
                    'status' => true,
                    'message' => 'OTP Send Successfully!',
                    'data' => $otp
                ], 200);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'You are not exist'
                ], 401);
            }
        } elseif ('resend_otp') {
            $otp = '1235';
            $otp_expire = Carbon::now()->addMinute(5);
            $save_otp = DB::table('students')->where('contact_number', $request->phone_no)->orwhere('alternate_contact_number', $request->phone_no)->update([
                'otp' => $otp,
                'otp_expire' => $otp_expire
            ]);

            return response()->json([
                'status' => true,
                'message' => 'New OTP Send Successfully!',
                'data' => $otp
            ], 200);
        }
    }

    public function verify_otp(request $request)
    {
        $validator = validator::make($request->all(), [
            'phone_no' => 'required',
            'otp' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ], 401);
        }
        $verify_otp = DB::table('students')
            ->where(function ($query) use ($request) {
                $query->where('contact_number', $request->phone_no)
                    ->orWhere('alternate_contact_number', $request->phone_no);
            })
            ->where('otp', $request->otp)
            ->first();
        if ($verify_otp) {
            return response()->json([
                'statu' => true,
                'message' => 'OTP Verify Successfully',
                'data' => $verify_otp
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Invalid OTP'
            ], 401);
        }
        if (Carbon::now()->greaterThan($verify_otp->otp_expire)) {
            return response()->json([
                'status' => false,
                'message' => 'Expired otp'
            ]);
        }
    }

    public function sendmail_otp(request $request)
    {
        $validator = validator::make($request->all(), [
            'btn_typ' => 'required',
            'email' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ], 401);
        }
        if ($request->btn_typ == 'send_otp') {
            $check = DB::table('students')->where('email_address', $request->email)->exists();
            if ($check) {
                $otp = rand(1111, 9999);
                $otp_expire = Carbon::now()->addMinute(5);
                $save_otp = DB::table('students')->where('email_address', $request->email)->update([
                    'otp' => $otp,
                    'otp_expire' => $otp_expire
                ]);

                Mail::raw("Your OTP is: $otp (valid for 5 minutes)", function ($message) use ($request) {
                    $message->to($request->email)
                        ->subject('Your Login OTP');
                });
                // Send email
                $userName = strstr($request->email, '@', true);
                Mail::to($request->email)->send(new OtpMail($otp, $userName));

                return response()->json([
                    'status' => true,
                    'message' => 'OTP Send Your Email Successfully!'
                ], 200);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'You are not exist'
                ], 401);
            }
        } elseif ('resend_otp') {
            $otp = rand(1111, 9999);
            $otp_expire = Carbon::now()->addMinute(5);
            $save_otp = DB::table('students')->where('email_address', $request->email)->update([
                'otp' => $otp,
                'otp_expire' => $otp_expire
            ]);

            Mail::raw("Your OTP is: $otp (valid for 5 minutes)", function ($message) use ($request) {
                $message->to($request->email)
                    ->subject('Your Login OTP');
            });
            // Send email
            $userName = strstr($request->email, '@', true);
            Mail::to($request->email)->send(new OtpMail($otp, $userName));


            return response()->json([
                'status' => true,
                'message' => 'New OTP Send Your Email Successfully!'
            ], 200);
        }
    }

    public function verifymail_otp(request $request)
    {
        $validator = validator::make($request->all(), [
            'email' => 'required',
            'otp' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ], 401);
        }
        $verify_otp = DB::table('students')
            ->where('email_address', $request->email)
            ->where('otp', $request->otp)
            ->first();

        if ($verify_otp) {
            if (Carbon::now()->greaterThan($verify_otp->otp_expire)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Expired otp'
                ]);
            }
            return response()->json([
                'statu' => true,
                'message' => 'OTP Verify Successfully',
                'data' => $verify_otp
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Invalid OTP'
            ], 401);
        }
    }
}
