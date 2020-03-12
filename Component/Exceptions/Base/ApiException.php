<?php
/**
 *
 * User: nintenichi
 * Date: 2019-08-08 16:55
 * Email: <rentianyi@homme-inc.com>
 */

namespace app\Exceptions\Base;

use Exception;

/**
 * 基础异常类，所有API接口的异常类将继承该类
 * Class ApiException
 * @package app\Exceptions\Base
 */
class ApiException extends Exception implements ApiExceptionInterface
{

}
