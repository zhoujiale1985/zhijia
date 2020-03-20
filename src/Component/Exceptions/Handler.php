<?php

namespace ZhijiaCommon\Exceptions;

use ZhijiaCommon\Component\ResponseSupport;
use ZhijiaCommon\Exceptions\Base\ApiExceptionInterface;
use Exception;

class Handler
{
    use ResponseSupport;

    /**
     * Report or log an exception.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        //parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Exception $exception
     * @return \Exception|object
     */
    public function render(Exception $exception)
    {
        //捕捉API接口异常并返回
        if ($exception instanceof ApiExceptionInterface) {
            $this->returnCode = $exception->getCode();
            $this->msg        = $exception->getMessage();
            return $this->jsonError();
        }
    }
}
