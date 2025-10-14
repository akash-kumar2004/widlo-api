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
        $studentId = $user->id;


        $attendance = DB::table('attendance')
            ->where('id', $studentId)->get();

        if ($attendance) {
            return response()->json([
                'status' => true,
                'message' => 'Student as Present',
                'data' => $attendance
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Student Absent or Gateway not in classroom'
            ], 200);
        }
    }
}
