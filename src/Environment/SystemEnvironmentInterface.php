<?php
declare(strict_types=1);

namespace IfCastle\Application\Environment;

use IfCastle\Application\Coroutine\CoroutineContextInterface;
use IfCastle\Application\EngineInterface;

interface SystemEnvironmentInterface
{
    /**
     * @var string
     */
    final public const string SYSTEM_ENVIRONMENT = 'systemEnvironment';
    
    /**
     * Role
     */
    public const string EXECUTION_ROLES           = 'execution_roles';
    
    public function getEngine(): EngineInterface;
    
    public function getCoroutineContext(): ?CoroutineContextInterface;
    
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