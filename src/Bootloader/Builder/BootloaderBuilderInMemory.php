<?php
declare(strict_types=1);

namespace IfCastle\Application\Bootloader\Builder;

use IfCastle\DI\ConfigInterface;

final class BootloaderBuilderInMemory extends BootloaderBuilderAbstract
{
    public function __construct(
        protected readonly string $appDirectory,
        protected readonly string $applicationType,
        protected readonly array $bootloaders   = [],
        protected readonly array $config        = []
    ) {}
    
    #[\Override]
    protected function fetchBootloaders(): iterable
    {
        return $this->bootloaders;
    }
    
    #[\Override]
    protected function initConfigurator(): ConfigInterface
    {
        return new ConfigInMemory($this->config);
    }
}