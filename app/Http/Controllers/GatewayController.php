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



//env
// APP_NAME=PostApp
// APP_ENV=local
// APP_KEY=base64:GJbRR68jvTU+v3qvzXTnw+bBFLDcpl5B6FcfVdgqtgg=
// APP_DEBUG=true
// APP_TIMEZONE=IST
// APP_URL=http://192.168.10.72

// APP_LOCALE=en
// APP_FALLBACK_LOCALE=en
// APP_FAKER_LOCALE=en_US

// APP_MAINTENANCE_DRIVER=file
// # APP_MAINTENANCE_STORE=database

// PHP_CLI_SERVER_WORKERS=4

// BCRYPT_ROUNDS=12

// LOG_CHANNEL=stack
// LOG_STACK=single
// LOG_DEPRECATIONS_CHANNEL=null
// LOG_LEVEL=debug

// DB_CONNECTION=mysql
// DB_HOST=127.0.0.1
// DB_PORT=3306
// # DB_DATABASE=postapp
// DB_DATABASE=widlo
// DB_USERNAME=root
// DB_PASSWORD=

// SESSION_DRIVER=database
// SESSION_LIFETIME=120
// SESSION_ENCRYPT=false
// SESSION_PATH=/
// SESSION_DOMAIN=null

// BROADCAST_CONNECTION=log
// FILESYSTEM_DISK=local
// QUEUE_CONNECTION=database

// CACHE_STORE=database
// CACHE_PREFIX=

// MEMCACHED_HOST=127.0.0.1

// REDIS_CLIENT=phpredis
// REDIS_HOST=127.0.0.1
// REDIS_PASSWORD=null
// REDIS_PORT=6379

// MAIL_MAILER=smtp
// MAIL_HOST=smtp.gmail.com
// MAIL_PORT=587
// MAIL_USERNAME=twomadengg@gmail.com
// MAIL_PASSWORD=kidmrvcrrxirsloj
// MAIL_ENCRYPTION=tls
// MAIL_FROM_ADDRESS="hello@example.com"
// MAIL_FROM_NAME="${APP_NAME}"
// CONTACT_EMAIL="twomadengg@gmail.com"
// ADMIN_EMAIL="twomadengg@gmail.com"

// AWS_ACCESS_KEY_ID=
// AWS_SECRET_ACCESS_KEY=
// AWS_DEFAULT_REGION=us-east-1
// AWS_BUCKET=
// AWS_USE_PATH_STYLE_ENDPOINT=false

// VITE_APP_NAME="${APP_NAME}"
// API_KEY=WQgJeefToJdNfzJXbN9dacq9hpXDuP91jqxw8BxTB1NXQVyBb0D84ZzeKZzshu7Z
