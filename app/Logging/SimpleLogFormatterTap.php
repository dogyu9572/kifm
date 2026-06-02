<?php

namespace App\Logging;

use Monolog\Formatter\LineFormatter;
use Monolog\Handler\FormattableHandlerInterface;
use Illuminate\Log\Logger as IlluminateLogger;
use Monolog\Logger;

class SimpleLogFormatterTap
{
    public function __invoke(IlluminateLogger|Logger $logger): void
    {
        $monolog = $logger instanceof IlluminateLogger ? $logger->getLogger() : $logger;

        // 스택트레이스를 제외한 단일 라인 로그 포맷
        $formatter = new LineFormatter(
            "[%datetime%] %level_name%: %message% %context%\n",
            'Y-m-d H:i:s',
            false,
            true,
            false
        );

        foreach ($monolog->getHandlers() as $handler) {
            if ($handler instanceof FormattableHandlerInterface) {
                $handler->setFormatter($formatter);
            }
        }
    }
}
