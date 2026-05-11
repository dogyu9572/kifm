<?php

namespace App\Logging;

use Monolog\Formatter\LineFormatter;
use Monolog\Handler\FormattableHandlerInterface;
use Monolog\Logger;

class SimpleLogFormatterTap
{
    public function __invoke(Logger $logger): void
    {
        // 스택트레이스를 제외한 단일 라인 로그 포맷
        $formatter = new LineFormatter(
            "[%datetime%] %level_name%: %message% %context%\n",
            'Y-m-d H:i:s',
            false,
            true
        );

        foreach ($logger->getHandlers() as $handler) {
            if ($handler instanceof FormattableHandlerInterface) {
                $handler->setFormatter($formatter);
            }
        }
    }
}

