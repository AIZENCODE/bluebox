<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class WebhookController extends Controller
{

    public function github(Request $request)
    {
        putenv('HOME=/home/blueboxdev');
        putenv('PATH=/usr/local/bin:/usr/bin:/bin');
        shell_exec('dploy deploy master');
        shell_exec('yes');

        return response()->json([
            'status' => 'success',
            'message' => 'Deployment triggered successfully.'
        ])->setStatusCode(200);
    }
}
