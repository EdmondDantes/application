<?php
declare(strict_types=1);

namespace IfCastle\Application\Bootloader;

use IfCastle\Application\Environment\SystemEnvironmentInterface;
use IfCastle\DesignPatterns\ExecutionPlan\BeforeAfterExecutor;
use IfCastle\DI\BuilderInterface;
use IfCastle\DI\ContainerBuilder;
use IfCastle\DI\DisposableInterface;
use IfCastle\Application\RequestEnvironment\Builder\BuilderInterface as RequestEnvironmentBuilderInterface;

class BootloaderExecutor            extends BeforeAfterExecutor
                                    implements BootloaderExecutorInterface, DisposableInterface
{
    protected BuilderInterface $systemEnvironmentBootBuilder;
    protected BuilderInterface $requestEnvironmentBootBuilder;
    
    protected SystemEnvironmentInterface|null $systemEnvironment = null;
    protected RequestEnvironmentBuilderInterface|null $requestEnvironmentBuilder = null;
    
    protected mixed $startApplicationHandler = null;
    
    public function __construct()
    {
        parent::__construct(new HandlerExecutor);
        // Main stage
        $this->addHandler($this->startApplication(...));
        
        $this->initBuilders();
    }
    
    #[\Override]
    public function dispose(): void
    {
        $this->stages               = [];
    }
    
    #[\Override]
    public function getSystemEnvironmentBootBuilder(): BuilderInterface
    {
        return $this->systemEnvironmentBootBuilder;
    }
    
    #[\Override]
    public function getRequestEnvironmentBootBuilder(): BuilderInterface
    {
        return $this->requestEnvironmentBootBuilder;
    }
    
    #[\Override]
    public function defineStartApplicationHandler(callable $handler): static
    {
        $this->startApplicationHandler   = $handler;
        return $this;
    }
    
    protected function initBuilders(): void
    {
        $this->systemEnvironmentBootBuilder     = new ContainerBuilder();
        $this->requestEnvironmentBootBuilder    = new ContainerBuilder();
    }
    
    protected function startApplication(): void
    {
        if($this->startApplicationHandler === null) {
            return;
        }
        
        // Build system environment
        
        // Start application
        $startApplicationHandler    = $this->startApplicationHandler;
        $this->startApplicationHandler = null;
        
        $startApplicationHandler($systemEnvironment);
    }
}