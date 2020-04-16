<?php
/**
 * Created by PhpStorm.
 * User: zhouhui
 * Date: 2020/4/6
 * Time: 18:36
 */

namespace ZhijiaCommon\Utils;


use Monolog\Formatter\LineFormatter;

class ZhidianLogFormatter {

    public function __invoke($logger) {

        foreach ($logger->getHandlers() as $handler) {
            $handler->setFormatter(new LineFormatter("[%datetime%]%level_name% %message% %context% %extra%\n", 'Y-m-d H:i:s', true, true));
        }
    }

}