<?php
declare(strict_types=1);

namespace IfCastle\Application\Bootloader;

use IfCastle\Application\Environment\SystemEnvironmentInterface;
use IfCastle\DesignPatterns\ExecutionPlan\BeforeAfterExecutorInterface;
use IfCastle\DI\BuilderInterface;
use IfCastle\Application\RequestEnvironment\Builder\BuilderInterface as RequestEnvironmentBuilderInterface;

interface BootloaderExecutorInterface extends BeforeAfterExecutorInterface
{
    public function getSystemEnvironmentBootBuilder(): BuilderInterface;
    
    public function getRequestEnvironmentBootBuilder(): BuilderInterface;
    
    public function getSystemEnvironment(): SystemEnvironmentInterface;
    
    public function getRequestEnvironmentBuilder(): RequestEnvironmentBuilderInterface;
}