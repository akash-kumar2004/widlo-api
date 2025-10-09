<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class GatewayController extends Controller
{
    public function update_gateway(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'gateway_id' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
            'tags' => 'required | array'  //mac_id
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ], 401);
        }
        try {
            $now = Carbon::now();

            DB::table('gateways')
                ->where('gateway_id', $request->gateway_id)
                ->update([
                    'latitude' => $request->latitude,
                    'longitude' => $request->longitude,
                    'last_seen' => $now,
                ]);

            $gateways_cls = DB::table('gateways')
                ->where('gateway_id', $request->gateway_id)
                ->first();

            if (is_array($request->tags) || is_object($request->tags)) {
                foreach ($request->tags as $tag) {
                    // Update student location and gateway info
                    DB::table('students')
                        ->where('tag_id', $tag['tag_id'])
                        ->update([
                            'gateway_id' => $request->gateway_id,
                            'latitude' => $request->latitude,
                            'longitude' => $request->longitude,
                            'last_seen' => $now
                        ]);

                    // Fetch student record (expecting only one per tag_id)
                    $student = DB::table('students')
                        ->where('tag_id', $tag['tag_id'])
                        ->first();

                    $noAttendanceToday  = !DB::table('attendance')
                        ->where('id', $student->id)
                        ->whereDate('created_at', today())
                        ->exists();

                    // Check if student exists
                    if ($student && $gateways_cls->installed_at === 'classroom' && $noAttendanceToday) {
                        DB::table('attendance')->insert([
                            'student_id' => $student->id,
                            'status' => 'Present',
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            } else {
                DB::table('students')
                    ->where('tag_id', $request->tags)
                    ->update([
                        'gateway_id' => $request->gateway_id,
                        'latitude' => $request->latitude,
                        'longitude' => $request->longitude,
                        'last_seen' => $now
                    ]);
            }

            $model = DB::table('gateways')
                ->where('gateway_id', $request->gateway_id)
                ->first();


            // $status = Carbon::parse($model->last_seen) >= Carbon::now()->subMinutes(2) ? 'Online' : 'Offline';

            return response()->json([
                'status' => true,
                'message' => 'Gateway and Student Data Updated Successfully'
                // 'data' => $status
            ]);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }
}
