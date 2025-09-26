<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;

class StudentController extends Controller
{
    public function getidcard(Request $request)
    {
        try {
            $student = $request->user();
            if ($student) {
                $class = DB::table('classrooms')->where('classroom_name', $student->class_section)->first();
                $bus = DB::table('schoolbuses')->where('bus_number', $student->assign_bus)->first();
                $class_gateway = DB::table('gateways')->where('gateway_name', $class->assign_gateway)->first();
                $bus_gateway = DB::table('gateways')->where('gateway_name', $bus->assign_gateway)->first();
                $busroute = DB::table('busroutes')->where('gateway_name', $bus->assign_route)->first();

                return response()->json([
                    'status' => true,
                    'message' => 'Student Details Fetch Successfully.',
                    'data' => [$student, $class, $bus]
                ], 200);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Student Details Not Found!.'
                ], 401);
            }
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
}
