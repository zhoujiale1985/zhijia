<?php

namespace App\Utils;

/**
 * 通用工具类
 */
class CommonUtils
{
  public function getRandomNum($length) {
    $nums = '0123456789';
    $rand = '';
    for ($i = 0; $i < $length; $i++) {
      $rand .= $nums[mt_rand(0, strlen($nums) -1)];
    }
    return $rand;
  }

  public function getRandomString($len) {
    $chars = "ABCDEFGHJKMNPQRSTWXYZabcdefhijkmnprstwxyz2345678";
    $pwd = '';
    for ($i = 0; $i < $len; $i++) {
      $pwd .= $chars[mt_rand(0, strlen($chars) -1)];
    }
    return $pwd;
  }
}
