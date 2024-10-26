<?php

declare(strict_types=1);

namespace IfCastle\Application\Bootloader;

use IfCastle\DesignPatterns\ExecutionPlan\HandlerExecutorInterface;
use IfCastle\DI\AutoResolverInterface;

final readonly class HandlerExecutor implements HandlerExecutorInterface
{
    private \WeakReference $bootloaderContext;

    public function __construct(BootloaderContextInterface $bootloaderContext)
    {
        $this->bootloaderContext    = \WeakReference::create($bootloaderContext);
    }


    #[\Override]
    public function executeHandler(mixed $handler, string $stage, mixed ...$parameters): mixed
    {
        $bootloaderContext          = $this->bootloaderContext->get();

        if ($bootloaderContext === null) {
            return null;
        }

        if ($handler instanceof BootloaderContextRequiredInterface) {
            $handler->setBootloaderContext($bootloaderContext);
        }

        if ($handler instanceof AutoResolverInterface) {
            $handler->resolveDependencies(
                $bootloaderContext->getSystemEnvironment()
                ?? throw new \Exception('System environment is required for AutoResolverInterface handler: ' . $handler::class)
            );
        }

        if (\is_callable($handler)) {
            return $handler();
        }

        return null;
    }
}
