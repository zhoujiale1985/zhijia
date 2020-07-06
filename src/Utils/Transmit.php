<?php
namespace ZhijiaCommon\Utils;

use GuzzleHttp\Client;
use Redis;
use Zipkin\Propagation\DefaultSamplingFlags;
use Zipkin\Propagation\Map;
use Zipkin\Timestamp;

class Transmit
{
    # http调用是否生成zipkin child span
    private $transmitZipkinSpan = false;
    private $mode = 'http';

    public function __construct($mode='http')
    {
        $this->mode = $mode;
        $tansmitFlag = env('TRANSMIT_ZIPKIN_SPAN');
        if($tansmitFlag === true){
            $this->transmitZipkinSpan = $tansmitFlag;
        }
    }

    /**
     * 生成zipkin child span 并且返回结果
     * @param string $spanName span 名称
     * @param string $reqType http method:GET|POST
     * @param string $serviceName 服务请求url连接
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function reqWithZipkin($spanName='child_span_name', $reqType='POST', $serviceName='servicename.com', $token='', $filter=[])
    {

        return ZipkinUtils::requestWithZipkin($spanName, $reqType, $serviceName, $token, $filter);
    }

    /**
     * @param $url
     * @param $filter
     * @param $token
     * @param bool $is_close
     * @param string $mode 'http'|'cli'
     * @return mixed|\Psr\Http\Message\ResponseInterface
     */
    public function otherPost($url, $filter, $token, $is_close = false)
    {
        if($this->transmitZipkinSpan && $this->mode=='http'){
            return $this->reqWithZipkin('transmit_otherpost_span', 'POST', $url, $token, $filter);
        }

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

    /**
     * @param $url
     * @param $token
     * @param string $mode 'http'|'cli'
     * @return mixed|\Psr\Http\Message\ResponseInterface|void
     */
    public function otherGet($url, $token)
    {
        if($this->transmitZipkinSpan && $this->mode=='http'){
            return $this->reqWithZipkin('transmit_otherget_span', 'GET', $url, $token);
        }

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