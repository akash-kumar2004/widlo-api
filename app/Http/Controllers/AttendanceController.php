<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function get_attendance(Request $request)
    {
        $user = $request->user();
        $gatewayId = $user->gateway_id;
        $tagId = $user->tag_id;
        $studentId = $user->student_id;

        
        $gateway = DB::table('gateways')
            ->where('gateway_id', $gatewayId)
            ->select('installed_at')
            ->first();

        if ($gateway && $gateway->installed_at === 'classroom') {

            DB::table('attendance')->insert([
                'student_id' => $studentId,
                'tag_id' => $tagId,
                'gateway_id' => $gatewayId,
                'status' => 'Present',
                'created_at' => now(),
                'updated_at' => now()
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Student marked as Present',
                'data' => [
                    'student_id' => $studentId,
                    'gateway_id' => $gatewayId,
                    'status' => 'Present'
                ]
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Student Absent or Gateway not in classroom'
            ], 200);
        }
    }
}
