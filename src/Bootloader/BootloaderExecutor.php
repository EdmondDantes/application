<?php
declare(strict_types=1);

namespace IfCastle\Application\Bootloader;

use IfCastle\Application\RequestEnvironment\Builder\RequestEnvironmentBuilder;
use IfCastle\DesignPatterns\ExecutionPlan\BeforeAfterExecutor;
use IfCastle\DI\BuilderInterface;
use IfCastle\DI\ContainerBuilder;
use IfCastle\DI\DisposableInterface;
use IfCastle\Application\RequestEnvironment\Builder\RequestEnvironmentBuilderInterface;
use IfCastle\DI\Resolver;
use IfCastle\DI\ResolverInterface;

class BootloaderExecutor            extends BeforeAfterExecutor
                                    implements BootloaderExecutorInterface, DisposableInterface
{
    protected BuilderInterface                   $systemEnvironmentBootBuilder;
    protected RequestEnvironmentBuilderInterface $requestEnvironmentBuilder;
    
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
    public function getRequestEnvironmentBuilder(): RequestEnvironmentBuilderInterface
    {
        return $this->requestEnvironmentBuilder;
    }
    
    #[\Override]
    public function defineStartApplicationHandler(callable $handler): static
    {
        $this->startApplicationHandler   = $handler;
        return $this;
    }
    
    protected function initBuilders(): void
    {
        $this->systemEnvironmentBootBuilder = new ContainerBuilder();
        $this->requestEnvironmentBuilder    = new RequestEnvironmentBuilder();
    }
    
    protected function startApplication(): void
    {
        if($this->startApplicationHandler === null) {
            return;
        }
        
        // Build system environment
        $this->systemEnvironmentBootBuilder->bindObject(RequestEnvironmentBuilderInterface::class, $this->requestEnvironmentBuilder);
        $systemEnvironment          = $this->systemEnvironmentBootBuilder->buildContainer($this->getDependencyResolver());
        
        // Start application
        $startApplicationHandler    = $this->startApplicationHandler;
        $this->startApplicationHandler = null;
        
        $startApplicationHandler($systemEnvironment);
    }
    
    protected function getDependencyResolver(): ResolverInterface
    {
        return new Resolver;
    }
}