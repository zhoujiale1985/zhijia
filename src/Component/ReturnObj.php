<?php

namespace ZhijiaCommon\Component;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ReturnObj
{

    public static function ok()
    {
        return ['data' => 'ok', 'msg' => 'success', 'code' => 0];
    }

    public static function error($msg, $error_code = -1)
    {
        $response = ['data' => '', 'msg' => $msg ?: 'fail', 'code' => $error_code];
        ReturnObj::recordLog($response);
        return $response;
    }

    public static function okObj($obj, $code = 0)
    {
        $response = ['data' => $obj, 'msg' => 'success', 'code' => $code];
        ReturnObj::recordLog($response);
        return $response;
    }


    public static function jsonP($data, $callback)
    {
        return response()->json($data)->withCallback($callback);
    }

    public static function recordLog($response)
    {
//        $request = $_POST ? : $_GET; // get 或 post的请求参数
        $request = \request()->all();
        $url = $_SERVER['REQUEST_URI'];
        $token = $_SERVER['HTTP_TOKEN'] ?? '';
        Log::info('record request and response info:', ['token' => $token, 'url' => $url, 'request' => $request, 'response' => $response]);
    }
}
