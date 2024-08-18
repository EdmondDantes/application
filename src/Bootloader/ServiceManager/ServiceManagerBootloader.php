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
use IfCastle\ServiceManager\ServiceLocatorInterface;
use IfCastle\ServiceManager\ServiceLocatorPublicInternal;
use IfCastle\ServiceManager\ServiceLocatorPublicInternalInterface;

/**
 * The Service Manager is the only mandatory component that must always be initialized for the `SystemEnvironment`.
 * If it is not specified during the creation of the `BootloaderExecutor` object,
 * it is initialized by default using the `ServiceManagerBootloader` class.
 */
final class ServiceManagerBootloader implements BootloaderInterface
{
    #[\Override]
    public function buildBootloader(BootloaderExecutorInterface $bootloaderExecutor): void
    {
        $builder                    = $bootloaderExecutor->getBootloaderContext()->getSystemEnvironmentBootBuilder();
        
        $this->defineServiceLocatorPublicInternal($builder);
        $this->defineDescriptorRepository($builder);
        $this->defineDescriptorBuilder($builder);
        
        $bootloaderExecutor->addAfterHandler(new ServiceExecutorPublicInternalBootloader);
    }
    
    private function defineServiceLocatorPublicInternal(BuilderInterface $builder): void
    {
        if($builder->isBound(ServiceLocatorInterface::class)) {
            return;
        }
        
        $builder->bindConstructible(
            [ServiceLocatorInterface::class, ServiceLocatorPublicInternalInterface::class],
            ServiceLocatorPublicInternal::class
        );
    }
    
    private function defineDescriptorRepository(BuilderInterface $builder): void
    {
        if($builder->isBound(DescriptorRepositoryInterface::class)) {
            return;
        }
        
        $builder->bindConstructible(DescriptorRepositoryInterface::class, DescriptorRepository::class);
    }
    
    private function defineDescriptorBuilder(BuilderInterface $builder): void
    {
        if($builder->isBound(ServiceDescriptorBuilderInterface::class)) {
            return;
        }
        
        $builder->bindObject(ServiceDescriptorBuilderInterface::class, new ServiceDescriptorBuilderByReflection);
    }
}