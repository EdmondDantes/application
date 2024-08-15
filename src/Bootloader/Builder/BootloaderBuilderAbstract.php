<?php
declare(strict_types=1);

namespace IfCastle\Application\Bootloader\Builder;

use IfCastle\Application\Bootloader\BootloaderExecutor;
use IfCastle\Application\Bootloader\BootloaderExecutorInterface;
use IfCastle\Application\Bootloader\BootloaderInterface;
use IfCastle\Application\Bootloader\ServiceManager\ServiceManagerBootloader;
use IfCastle\Application\Environment\SystemEnvironmentInterface;
use IfCastle\DI\ConfigInterface;
use IfCastle\ServiceManager\ExecutorInterface;
use IfCastle\ServiceManager\ServiceLocatorInterface;

abstract class BootloaderBuilderAbstract implements BootloaderBuilderInterface
{
    protected readonly string $appDirectory;
    
    protected BootloaderExecutorInterface|null $bootloader = null;
    protected readonly string $applicationType;
    
    protected array $executionRoles;
    
    #[\Override]
    public function getApplicationDirectory(): string
    {
        return $this->appDirectory;
    }
    
    #[\Override]
    public function getApplicationType(): string
    {
        return $this->applicationType;
    }
    
    #[\Override]
    public function getExecutionRoles(): array
    {
        return $this->executionRoles;
    }
    
    #[\Override]
    public function build(): void
    {
        if($this->bootloader === null) {
            $configurator           = $this->initConfigurator();
            $this->bootloader        = new BootloaderExecutor($configurator, $this->applicationType);
            $this->defineExecutionRoles($configurator);
        }
        
        foreach ($this->fetchBootloaders() as $bootloaderClass) {
            if(false === class_exists($bootloaderClass)) {
                throw new \RuntimeException('Bootloader class not found: ' . $bootloaderClass);
            }
            
            $this->handleBootloaderClass($bootloaderClass);
        }
        
        $this->defineServiceManagerBootloader();
    }
    
    #[\Override]
    public function getBootloader(): BootloaderExecutorInterface
    {
        if($this->bootloader === null) {
            throw new \RuntimeException('Bootloader not built');
        }
        
        return $this->bootloader;
    }
    
    abstract protected function fetchBootloaders(): iterable;
    abstract protected function initConfigurator(): ConfigInterface;
    
    protected function handleBootloaderClass(string $bootloaderClass): void
    {
        $object                     = new $bootloaderClass();
        
        if(false === $object instanceof BootloaderInterface) {
            throw new \RuntimeException('Bootloader class must implement BootloaderInterface: ' . $bootloaderClass);
        }
        
        $object->buildBootloader($this->bootloader);
    }
    
    protected function defineExecutionRoles(ConfigInterface $configurator): void
    {
        foreach ($configurator->findSection(SystemEnvironmentInterface::EXECUTION_ROLES) ?? [] as $role => $value) {
            if(!empty($value)) {
                $executionRoles[]   = $role;
            }
        }
        
        $executionRoles[]           = $this->applicationType;
        $this->executionRoles       = array_unique($executionRoles);
    }
    
    protected function defineServiceManagerBootloader(): void
    {
        $builder                    = $this->bootloader->getBootloaderContext()->getSystemEnvironmentBootBuilder();
        
        if($builder->isBound(ServiceLocatorInterface::class, ExecutorInterface::class)) {
            return;
        }
        
        $this->handleBootloaderClass(ServiceManagerBootloader::class);
    }
}