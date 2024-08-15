<?php
declare(strict_types=1);

namespace IfCastle\Application\Bootloader\ServiceManager;

use IfCastle\Application\Bootloader\BootloaderExecutorInterface;
use IfCastle\Application\Bootloader\BootloaderInterface;
use IfCastle\DI\BuilderInterface;
use IfCastle\ServiceManager\DescriptorRepository;
use IfCastle\ServiceManager\DescriptorRepositoryInterface;
use IfCastle\ServiceManager\ServiceDescriptorBuilderByReflection;
use IfCastle\ServiceManager\ServiceDescriptorBuilderInterface;
use IfCastle\ServiceManager\ServiceLocatorPublicInternal;
use IfCastle\ServiceManager\ServiceLocatorPublicInternalInterface;

final class ServiceManagerBootloader implements BootloaderInterface
{
    #[\Override]
    public function buildBootloader(BootloaderExecutorInterface $bootloaderExecutor): void
    {
        $builder                    = $bootloaderExecutor->getBootloaderContext()->getSystemEnvironmentBootBuilder();
        
        $this->defineServiceLocatorPublicInternal($builder);
        $this->defineDescriptorRepository($builder);
        $this->defineDescriptorBuilder($builder);
    }
    
    private function defineServiceLocatorPublicInternal(BuilderInterface $builder): void
    {
        if($builder->isBound(ServiceLocatorPublicInternalInterface::class) !== null) {
            return;
        }
        
        $builder->bindConstructible(ServiceLocatorPublicInternalInterface::class, ServiceLocatorPublicInternal::class);
    }
    
    private function defineDescriptorRepository(BuilderInterface $builder): void
    {
        if($builder->isBound(DescriptorRepositoryInterface::class) !== null) {
            return;
        }
        
        $builder->bindConstructible(DescriptorRepositoryInterface::class, DescriptorRepository::class);
    }
    
    private function defineDescriptorBuilder(BuilderInterface $builder): void
    {
        if($builder->isBound(ServiceDescriptorBuilderInterface::class) !== null) {
            return;
        }
        
        $builder->bindObject(ServiceDescriptorBuilderInterface::class, new ServiceDescriptorBuilderByReflection);
    }
}