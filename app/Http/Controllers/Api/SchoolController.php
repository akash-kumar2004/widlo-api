<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;

class SchoolController extends Controller
{
    public function school_list()
    {
        try {
            $school_list = DB::table('schoolmgmt')->get();

            if ($school_list) {
                return response()->json([
                    'status' => true,
                    'message' => 'School Details Get Successfully.',
                    'data' => $school_list
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'School Details Not Found'
                ]);
            }
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }
}
