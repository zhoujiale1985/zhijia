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
    //基础请求
    public static $errorCodeList = [
        200 => '成功',
        403 => '权限不足',
        404 => '未找到页面',
    ];
    //通用模块
    public static $errorCodeCommonList = [
        1001 => '必要参数不可为空',
        1002 => '未找到相关数据',
        1003 => '删除失败，请重新尝试',
        1004 => '添加失败，请重新尝试',
        1005 => '编辑失败，请重新尝试',
        1006 => '您尚未进行修改任何',
        1007 => '参数错误',
        2001 => '用户数据过期，请重新登陆',
        2002 => '缺少token参数',
        2003 => '验证系统异常，请检查',
        2004 => '上传文件不能为空',
        2005 => '七牛云请求异常',
        2006 => '文件扩展名不符，请重新上传',
        2007 => '单文件大小超过文件上传最大限制（3MB）',
        2008 => '验证码失效',
        2009 => '验证码错误',
    ];
    ///供应商管理服务从10001开始
    public static $errorCodeSupplierList = [
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
    ///订单服务从20001开始
    public static $errorCodeOrderList = [
        20001 => '您已提交订单，请勿重复提交',
    ];
    ///商品服务从30001开始
    public static $errorCodeGoodsList = [
        30001 => '商品数据保持失败，请稍后再试',
    ];
    ///用户服务从40001开始
    public static $errorCodeUserList = [
        40001 => '用户数据保持失败，请稍后再试',
        40002 => '手机号或验证码错误',
        40003 => '账号信息错误',
        40004 => '绑定失败',
        40005 => '已绑定其他账户',
        40006 => '登录失败',
        40010 => '标签已存在，请重试',
        40011 => '标签的层级ID错误，请重试',
        40012 => '标签ID错误，只能删除三级标签下的用户',
        40013 => '标签ID错误，目前不支持删除一级标签'
    ];
    ///促销活动服务50001开始
    public static $errorCodeActivityList = [
        50001 => '活动数据保持失败，请稍后再试',
    ];
    ///卡券服务60001开始
    public static $errorCodeCouponsList = [
        60001 => '卡券数据保持失败，请稍后再试',
    ];
    ///评论服务70001开始
    public static $errorCodeCommentList = [
        70001 => '评论数据保持失败，请稍后再试',
    ];
    ///搜索服务80001开始
    public static $errorCodeSearchList = [
        80001 => '搜索数据保持失败，请稍后再试',
    ];
    ///数据看板dashboard服务90001开始
    public static $errorCodeDashBoardList = [
        90001 => '看板数据保持失败，请稍后再试',
    ];


    /**
     * Return API interface exception.
     * API控制器返回错误码及信息方法
     * @param $code
     * @throws ApiException
     */
    public function returnException($code): void
    {
        //如果不存在错误码则抛出异常
        if (!array_key_exists($code, $this->errorCodeList)) {
            throw new ApiException('未找到正确的错误码', 500);
        }
        //抛出错误异常
        throw new ApiException($this->errorCodeList[$code], $code);
    }
}
