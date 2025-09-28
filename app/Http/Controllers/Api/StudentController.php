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

                $class = DB::table('classrooms')
                    ->where('classroom_name', $student->class_section)
                    ->first();


                $bus = DB::table('schoolbuses')
                    ->where('bus_number', $student->assign_bus)
                    ->first();


                $bus_gateway = null;
                if ($bus) {
                    $bus_gateway = DB::table('gateways')
                        ->where('gateway_name', $bus->assign_gateway)
                        ->first();

                    $bus_route = DB::table('busroutes')
                        ->where('route_name', $bus->assign_route)
                        ->first();
                }


                $class_gateway = null;
                if ($class) {
                    $class_gateway = DB::table('gateways')
                        ->where('gateway_name', $class->assign_gateway)
                        ->first();
                }

                $responseData = [
                    'student' => $student,
                    'bus_detail' => $bus ? [
                        'bus'         => $bus,
                        'bus_gateway' => $bus_gateway ?: null,
                        'bus_route'   => $bus_route ?: null,
                    ] : null,
                    'class_details' => $class ? [
                        'class'         => $class,
                        'class_gateway' => $class_gateway ?: null,
                    ] : null,
                ];

                return response()->json([
                    'status'  => true,
                    'message' => 'Student Details Get Successfully.',
                    'data'    => $responseData
                ], 200);
            } else {
                return response()->json([
                    'status'  => false,
                    'message' => 'Student Details Not Found!.'
                ], 404);
            }
        } catch (Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }


    public function current_location(Request $request)
    {
        try {


            $student = $request->user();
            if ($student) {
                $responseData = [
                    'id'                => $student->id,
                    'student_name'      => $student->student_name,
                    'latitude'          => $student->latitude,
                    'longitude'         => $student->longitude,
                    'gateway_id'        => $student->gateway_id,
                    'last_seen'         => $student->last_seen,
                ];


                return response()->json([
                    'status' => true,
                    'message' => "Student data get successfully.",
                    'data' => $responseData
                ]);
            } else {
                return response()->json([
                    'status'  => false,
                    'message' => 'Student not found!'
                ], 404);
            }
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
}
