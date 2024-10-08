<?php
declare(strict_types=1);

namespace IfCastle\Application\Bootloader;

use IfCastle\DesignPatterns\ExecutionPlan\BeforeAfterExecutorInterface;

interface BootloaderExecutorInterface extends BeforeAfterExecutorInterface
{
    public function getBootloaderContext(): BootloaderContextInterface;
    
    public function defineStartApplicationHandler(callable $handler): static;
}