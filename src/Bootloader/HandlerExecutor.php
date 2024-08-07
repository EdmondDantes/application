<?php
declare(strict_types=1);

namespace IfCastle\Application\Bootloader;

use IfCastle\DesignPatterns\ExecutionPlan\HandlerExecutorInterface;
use IfCastle\DI\AutoResolverInterface;

final class HandlerExecutor         implements HandlerExecutorInterface
{
    private \WeakReference $bootloaderContext;
    
    public function __construct(BootloaderContextInterface $bootloaderContext)
    {
        $this->bootloaderContext    = \WeakReference::create($bootloaderContext);
    }
    
    
    #[\Override]
    public function executeHandler(mixed $handler, string $stage): void
    {
        $bootloaderContext          = $this->bootloaderContext->get();
        
        if($bootloaderContext === null) {
            return;
        }
        
        if($handler instanceof AutoResolverInterface) {
            $handler->resolveDependencies($bootloaderContext);
        }
        
        if(is_callable($handler)) {
            $handler();
        }
    }
}