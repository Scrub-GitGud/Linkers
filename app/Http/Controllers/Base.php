<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class Base extends Controller
{
    public static function SUCCESS($msg = 'Task successfull!!', $data = []) {
        return response()->json([
            'success' => true,
            'code' => 200,
            'message' => $msg,
            'data' => $data
        ]);
    }

    public static function ERROR($msg = 'An error occurred!!', $error = []) {
        return response()->json([
            'success' => false,
            'code' => 400,
            'message' => $msg,
            'data' => $error
        ]);
    }
}
