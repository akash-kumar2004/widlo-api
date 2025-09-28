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
                    'id'            => $student->id,
                    'student_name'  => $student->name,
                    'address'       => $student->address,
                    'bus_detail'    => $bus ? [
                        'id'           => $bus->id,
                        'bus_number'   => $bus->bus_number,
                        'driver_name'  => $bus->driver_name,
                        'bus_gateway'  => $bus_gateway ? [
                            'id'          => $bus_gateway->id,
                            'gateway_id'  => $bus_gateway->gateway_id,
                            'gateway_name' => $bus_gateway->gateway_name,
                        ] : null,
                        'bus_route'    => $bus_route ? [
                            'id'         => $bus_route->id,
                            'route_number'   => $bus_route->route_number,
                            'route_name' => $bus_route->route_name,
                            'route_lat_lng_list' => $bus_route->route_lat_lng_list,
                        ] : null,
                    ] : null,
                    'class_details' => $class ? [
                        'id'               => $class->id,
                        'classroom_name'   => $class->classroom_name,
                        'class_teacher_name' => $class->class_teacher_name,
                        'class_gateway'    => $class_gateway ? [
                            'id'          => $class_gateway->id,
                            'gateway_id'  => $class_gateway->gateway_id,
                            'gateway_name' => $class_gateway->gateway_name,
                        ] : null,
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
                    'student_name'      => $student->student_name,   // or $student->student_name
                    'latitude'          => $student->latitude,
                    'longitude'         => $student->longitude,
                    'gateway_id'        => $student->gateway_id,
                    // 'gateway_type'      => $student->gateway_type,
                    'lastseen_datetime' => $student->lastseen_datetime,
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
