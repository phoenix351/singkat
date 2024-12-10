<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TokenController extends Controller
{
    public function get_csrf_token(Request $request)
    {
        $token = $request->session()->token();

        $token = csrf_token();
        return response()->json($token, 200);
    }
}
