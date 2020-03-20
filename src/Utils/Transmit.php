<?php


namespace ZhijiaCommon\Utils;


class Transmit
{
  public function otherPost($url, $filter, $token, $is_close = false)
  {
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array(
      'Content-Type: application/json; charset=utf-8',
      'newadmin-token:'.$token
    ));
    //设置post数据
    $post_data = $filter;
    $post_data = json_encode($post_data);
    //post提交的数据
    curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);
    //执行命令
    $data = curl_exec($curl);
    //显示获得的数据 supplier_id
    $data = json_decode($data, true);
    if ($is_close) {
      //关闭URL请求
      curl_close($curl);
    }
    return $data;
  }

  public function otherGet($url, $token)
  {
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);//设置抓取的ur
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER, [
      'newadmin-token:'.$token
    ]);
    $data = curl_exec($curl);
    $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    //关闭URL请求
    curl_close($curl);
    //显示获得的数据
    if ($http_code == 200) {
      $data = json_decode($data, true);
      return $data;
    } else {
      return;
    }
  }
}