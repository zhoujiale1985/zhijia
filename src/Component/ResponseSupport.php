<?php
/**
 * This is response support trait.
 * User: nintenichi
 * Date: 2019-08-06 11:04
 * Email: <rentianyi@homme-inc.com>
 */

namespace ZhijiaCommon\Component;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

/**
 * Trait ResponseSupport
 * 响应处理 Trait
 * @package ZhijiaCommon\Component
 */
trait ResponseSupport
{
    /**
     * 响应码
     * @var int
     */
    public $returnCode = 200;

    /**
     * 响应的数据
     * @var array
     */
    public $data = [];

    /**
     * 响应的消息
     * @var string
     */
    public $msg = '';

    /**
     * 响应的配置
     * @var array
     */
    public $opts = [];

    /**
     * 手动对列表进行分页
     * @param $list 要分页的数据
     * @param int $current_page 当前页码
     * @param int $page_size 页码数据量
     * @return array|int
     */
    public function autoPaginate($list,$current_page=1,$page_size=10)
    {
        if(empty($list)) return 0;
        //page 从1开始
        $start = ($current_page - 1) * $page_size;
        //$end = (($current_page - 1) * $page_size) + $page_size;
        $result = array_slice($list,$start,$page_size);
        return $result;
    }

    /**
     * 统一响应格式，并以JSON的形式响应数据
     * @return object
     */
    public function jsonResponse() : object
    {
        $response = [
            'code' => $this->returnCode,
            'msg'  => $this->msg,
            'data' => $this->data,
            'opts' => $this->opts,
        ];
        return Response()->json($response);
    }

    /**
     * 统一错误响应格式
     * @return object
     */
    public function jsonError() : object
    {
        $response = [
            'code' => $this->returnCode,
            'msg'  => $this->msg,
        ];
        $httpStatus = 200;
        if (array_key_exists($this->returnCode, Response::$statusTexts)) {
            $httpStatus = $this->returnCode;
        }
        return Response()->json($response,$httpStatus);
    }
}
