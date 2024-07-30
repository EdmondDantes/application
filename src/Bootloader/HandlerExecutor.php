<?php
declare(strict_types=1);

namespace IfCastle\Application\Bootloader;

use IfCastle\DesignPatterns\ExecutionPlan\HandlerExecutorInterface;

final class HandlerExecutor implements HandlerExecutorInterface
{
    #[\Override]
    public function executeHandler(mixed $handler, string $stage): void
    {
        if(is_callable($handler)) {
            $handler();
        }
    }
}