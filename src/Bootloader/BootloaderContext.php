<?php
declare(strict_types=1);

namespace IfCastle\Application\Bootloader;

use IfCastle\Application\Bootloader\Builder\PublicEnvironmentBuilderInterface;
use IfCastle\Application\RequestEnvironment\Builder\RequestEnvironmentBuilderInterface;
use IfCastle\DI\BuilderInterface;
use IfCastle\DI\ConfigInterface;
use IfCastle\DI\Container;

class BootloaderContext             extends Container
                                    implements BootloaderContextInterface
{
    #[\Override]
    public function getApplicationDirectory(): string
    {
        return $this->container[self::APPLICATION_DIRECTORY] ?? '';
    }
    
    #[\Override]
    public function getApplicationType(): string
    {
        return $this->container[self::APPLICATION_TYPE] ?? '';
    }
    
    #[\Override]
    public function getApplicationConfig(): ConfigInterface
    {
        return $this->resolveDependency(ConfigInterface::class);
    }
    
    #[\Override]
    public function getSystemEnvironmentBootBuilder(): BuilderInterface
    {
        return $this->resolveDependency(BuilderInterface::class);
    }
    
    #[\Override]
    public function getPublicEnvironmentBootBuilder(): PublicEnvironmentBuilderInterface
    {
        return $this->resolveDependency(PublicEnvironmentBuilderInterface::class);
    }
    
    #[\Override]
    public function getRequestEnvironmentBuilder(): RequestEnvironmentBuilderInterface
    {
        return $this->resolveDependency(RequestEnvironmentBuilderInterface::class);
    }
}