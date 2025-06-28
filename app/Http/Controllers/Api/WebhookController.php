<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class WebhookController extends Controller
{

    public function github(Request $request)
    {
        $payload = $request->getContent();

        $signature = 'sha1=' . hash_hmac('sha1', $payload, 'blueboxsubir');

        if ($request->header('X-Hub-Signature') !== $signature) {
            return response()->json(['status' => 'error', 'message' => 'Invalid signature'], 403);
        }

        putenv('HOME=/home/blueboxdev');
        putenv('PATH=/usr/local/bin:/usr/bin:/bin');
        shell_exec('dploy deploy master');
        // shell_exec('yes');

        return response()->json([
            'status' => 'success',
            'message' => 'Deployment triggered successfully.'
        ])->setStatusCode(200);
    }
}
