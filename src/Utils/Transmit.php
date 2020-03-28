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
            'newadmin-token:' . $token
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
            'newadmin-token:' . $token
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

    /**
     * 封装curl函数
     * @param $url
     * @param null $postData
     * @param null $cookie
     * @param null $ip
     * @param null $port
     * @param bool $isUtf8
     * @param null $auth
     * @param bool $onlyHeader
     * @return mixed
     */
    public static function curlRequest($url, $postData = null, $cookie = null, $ip = null, $port = null, $isUtf8 = false, $auth = null, $onlyHeader = false)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);

        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_TIMEOUT, 3);
        // 头信息不作为数据流输出
        if (!$onlyHeader) {
            curl_setopt($ch, CURLOPT_HEADER, 0);
        } else {
            curl_setopt($ch, CURLOPT_HEADER, 1);
            curl_setopt($ch, CURLOPT_NOBODY, 1);
        }
//        curl_setopt($ch, CURLOPT_FAILONERROR, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        if (!is_null($postData)) {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        }
        if (!is_null($cookie)) {
            curl_setopt($ch, CURLOPT_COOKIE, $cookie);
        }
        if (!is_null($ip)) {
            curl_setopt($ch, CURLOPT_PROXY, $ip);
        }
        if (!is_null($port)) {
            curl_setopt($ch, CURLOPT_PROXYPORT, $port);
        }

        if (!is_null($auth)) {
            curl_setopt($ch, CURLOPT_USERPWD, $auth);
        }
        if ($isUtf8) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json;charset=utf-8'));
        }

        if (parse_url($url)['scheme'] == 'https') {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        }

        $response = curl_exec($ch);
        if (false == $response || is_null($response)) {
            die('curl的问题：url 为' . $url . '具体错误为：' . curl_error($ch));//出问题，就发钉钉
        }
        curl_close($ch);
        return $response;
    }
}