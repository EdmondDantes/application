<?php
declare(strict_types=1);

namespace IfCastle\Application\Environment;

use IfCastle\Application\CoroutineContextInterface;
use IfCastle\Application\EngineInterface;
use IfCastle\Application\RequestEnvironment\RequestEnvironmentInterface;
use IfCastle\Application\ScheduleTimerInterface;
use IfCastle\DI\ContainerInterface;
use IfCastle\DI\DisposableInterface;

interface SystemEnvironmentInterface extends ContainerInterface, DisposableInterface
{
    public const string EXECUTION_ROLES = 'execution_roles';
    
    public function getEngine(): EngineInterface;
    
    public function getCoroutineContext(): CoroutineContextInterface|null;
    
    public function getScheduleTimer(): ScheduleTimerInterface|null;
    
    /**
     * Return current request env if exists
     */
    public function getRequestEnvironment(): RequestEnvironmentInterface|null;
    
    public function setRequestEnvironment(RequestEnvironmentInterface $requestEnvironment): void;
    
    public function isDeveloperMode(): bool;
    
    public function isTestMode(): bool;
    
    public function isWebServer(): bool;
    
    public function isJobProcess(): bool;
    
    public function getExecutionRoles(): array;
    
    public function isRoleWebServer(): bool;
    
    public function isRoleJobsServer(): bool;
}