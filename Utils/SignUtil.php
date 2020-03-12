<?php
namespace App\Utils;

class SignUtil {
    public static function getData($str) {
        if (!$str) {
            $str = "";
        }
        $data = md5($str . mt_rand(0, 100000) . time());
        return $data;
    }
}
