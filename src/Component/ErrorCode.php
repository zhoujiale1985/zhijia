<?php
/**
 *
 * User: nintenichi
 * Date: 2019-08-08 16:52
 * Email: <rentianyi@homme-inc.com>
 */
namespace ZhijiaCommon\Component;

use ZhijiaCommon\Exceptions\Base\ApiException;
use Exception;

/**
 * Trait ErrorCode
 * @package ZhijiaCommon\Component
 */
trait ErrorCode
{
    /**
     * Error Code List.
     * 错误码列表
     * @var array
     */
    private $errorCodeList = [
        //基础请求
        200 => '成功',
        403 => '权限不足',
        404 => '未找到页面',
        //指定模块请求
        //通用模块
        1001 => '必要参数不可为空',
        1002 => '未找到相关数据',
        1003 => '删除失败，请重新尝试',
        1004 => '添加失败，请重新尝试',
        1005 => '编辑失败，请重新尝试',
        1006 => '您尚未进行修改任何',



        2001 => '用户数据过期，请重新登陆',
        2002 => '缺少token参数',
        2003 => '验证系统异常，请检查',
        2004 => '上传文件不能为空',
        2005 => '七牛云请求异常',
        2006 => '文件扩展名不符，请重新上传',
        2007 => '单文件大小超过文件上传最大限制（3MB）',
        //供应商管理模块
        10001 => '供应商数据保存失败，请稍后再试',
        10002 => '供应商名称已存在，不可重复添加',
        10003 => '供应商已存在，请检查修改的供应商名称',
        10004 => '月结供应商数量为0',
        10005 => 'Order服务：月结供应商当前订单数量为空',
        10006 => 'Order服务：供应商的数据为空',
        10007 => '修改供应商结算状态失败，请稍后再试',
        10008 => '供应商不存在',
        //添加品牌模块
        10101 => '品牌数据保存失败',
        10102 => '品牌已经存在，不可重复添加',
        //参数合法模块
        15000 => '参数错误：type',
    ];

    /**
     * Return API interface exception.
     * API控制器返回错误码及信息方法
     * @param $code
     * @throws ApiException
     */
    public function returnException($code) : void
    {
        //如果不存在错误码则抛出异常
        if (!array_key_exists($code, $this->errorCodeList)) {
            throw new ApiException('未找到正确的错误码', 500);
        }
        //抛出错误异常
        throw new ApiException($this->errorCodeList[$code], $code);
    }
}
