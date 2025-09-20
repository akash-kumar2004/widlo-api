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
            'tag_id' => 'required | array'  //mac_id
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

            // DB::table('students')
            //     ->where('tag_id', $request->tag_id)
            //     ->update(['last_seen' => $now]);

            if (is_array($request->tag_id) || is_object($request->tag_id)) {
                foreach ($request->tag_id as $tag) {
                    DB::table('students')
                        ->where('tag_id', $tag['tag_id'])
                        ->update(['last_seen' => $now]);
                }
            } else {
                DB::table('students')
                    ->where('tag_id', $request->tag_id)
                    ->update(['last_seen' => $now]);
            }

            $model = DB::table('gateways')
                ->where('gateway_id', $request->gateway_id)
                ->first();

            if ($model) {
                $status = Carbon::parse($model->last_seen) >= Carbon::now()->subMinutes(2) ? 'Online' : 'Offline';
            } else {
                $status = 'Offline';
            }
            return response()->json([
                'status' => true,
                'message' => 'Gateway and Student Data Updated Successfully',
                'data' => $status
            ]);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }
}


