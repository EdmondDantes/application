<?php
declare(strict_types=1);

namespace IfCastle\Application\Bootloader;

use IfCastle\DesignPatterns\ExecutionPlan\BeforeAfterExecutorInterface;
use IfCastle\DI\BuilderInterface;

interface BootloaderExecutorInterface extends BeforeAfterExecutorInterface
{
    public function getSystemEnvironmentBootBuilder(): BuilderInterface;
    
    public function getRequestEnvironmentBootBuilder(): BuilderInterface;
}