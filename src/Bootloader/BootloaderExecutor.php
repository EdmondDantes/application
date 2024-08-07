<?php
declare(strict_types=1);

namespace IfCastle\Application\Bootloader;

use IfCastle\Application\Bootloader\Builder\PublicEnvironmentBuilderInterface;
use IfCastle\Application\Environment\PublicEnvironmentInterface;
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
    protected BootloaderContextInterface $bootloaderContext;
    protected mixed $startApplicationHandler = null;
    
    public function __construct()
    {
        $this->initContext();
        
        parent::__construct(new HandlerExecutor($this->bootloaderContext));
        
        // Main stage
        $this->addHandler($this->startApplication(...));
        
        $this->initContext();
    }
    
    #[\Override]
    public function getBootloaderContext(): BootloaderContextInterface
    {
        return $this->bootloaderContext;
    }
    
    
    #[\Override]
    public function dispose(): void
    {
        $this->stages               = [];
    }
    
    public function getSystemEnvironmentBootBuilder(): BuilderInterface
    {
        return $this->bootloaderContext->getSystemEnvironmentBootBuilder();
    }
    
    public function getRequestEnvironmentBuilder(): RequestEnvironmentBuilderInterface
    {
        return $this->bootloaderContext->getRequestEnvironmentBuilder();
    }
    
    #[\Override]
    public function defineStartApplicationHandler(callable $handler): static
    {
        $this->startApplicationHandler   = $handler;
        return $this;
    }
    
    protected function initContext(): void
    {
        $this->bootloaderContext = new BootloaderContext(new Resolver, [
            BootloaderExecutorInterface::class          => \WeakReference::create($this),
            BuilderInterface::class                     => new ContainerBuilder(),
            PublicEnvironmentBuilderInterface::class    => new ContainerBuilder(),
            RequestEnvironmentBuilderInterface::class   => new RequestEnvironmentBuilder()
        ]);
    }
    
    protected function startApplication(): void
    {
        if($this->startApplicationHandler === null) {
            return;
        }
        
        // Build system environment
        $this->getSystemEnvironmentBootBuilder()->bindObject(RequestEnvironmentBuilderInterface::class, $this->getRequestEnvironmentBuilder());
        $systemEnvironment          = $this->getSystemEnvironmentBootBuilder()->buildContainer($this->getDependencyResolver());
        
        $builder                    = $this->bootloaderContext->getPublicEnvironmentBootBuilder();
        
        $publicEnvironment          = $builder->buildContainer($this->getDependencyResolver(), $systemEnvironment, true);
        $systemEnvironment->set(PublicEnvironmentInterface::class, $publicEnvironment);
        
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