<?php
declare(strict_types=1);

namespace IfCastle\Application\Bootloader\ServiceManager;

use IfCastle\Application\Environment\PublicEnvironmentInterface;
use IfCastle\Application\Environment\SystemEnvironmentInterface;
use IfCastle\DI\AutoResolverInterface;
use IfCastle\DI\ContainerInterface;
use IfCastle\DI\DisposableInterface;
use IfCastle\ServiceManager\DescriptorRepository;
use IfCastle\ServiceManager\DescriptorRepositoryInterface;
use IfCastle\ServiceManager\ExecutorInterface;
use IfCastle\ServiceManager\RepositoryStorages\RepositoryReaderByScopeInterface;
use IfCastle\ServiceManager\RepositoryStorages\RepositoryReaderInterface;
use IfCastle\ServiceManager\ServiceDescriptorBuilderByReflection;
use IfCastle\ServiceManager\ServiceDescriptorBuilderInterface;
use IfCastle\ServiceManager\ServiceLocatorPublicInternal;
use IfCastle\ServiceManager\ServiceLocatorPublicInternalInterface;
use IfCastle\TypeDefinitions\Resolver\ContainerTypeResolver;

final class ServiceManagerBootloaderPublicInternal implements AutoResolverInterface, DisposableInterface
{
    private SystemEnvironmentInterface|null $systemEnvironment = null;
    
    #[\Override]
    public function resolveDependencies(ContainerInterface $container): void
    {
        if($container instanceof SystemEnvironmentInterface) {
            $this->systemEnvironment = $container;
        }
    }
    
    public function __invoke(): void
    {
        $sysEnv                     = $this->systemEnvironment;
        
        if($sysEnv === null) {
            throw new \Exception('System environment is required for ServiceManagerBootloader');
        }
        
        $publicEnvironment          = $sysEnv->resolveDependency(PublicEnvironmentInterface::class);
        
        $this->defineServiceLocatorPublicInternal($sysEnv);
        
        if(false === $sysEnv->hasDependency(ExecutorInterface::class)) {
            $sysEnv->set(ExecutorInterface::class, new InternalExecutorInitializer);
        }
        
        if(false === $publicEnvironment->hasDependency(ExecutorInterface::class)) {
            $publicEnvironment->set(ExecutorInterface::class, new PublicExecutorInitializer);
        }
    }
    
    private function defineServiceLocatorPublicInternal(SystemEnvironmentInterface $systemEnvironment): ServiceLocatorPublicInternalInterface
    {
        $serviceLocator             = $systemEnvironment->findDependency(ServiceLocatorPublicInternalInterface::class);
        
        if($serviceLocator !== null) {
            return $serviceLocator;
        }
        
        $serviceLocator             = new ServiceLocatorPublicInternal(
            $this->defineDescriptorRepository($systemEnvironment),
            $systemEnvironment,
            true
        );
        
        $systemEnvironment->set(ServiceLocatorPublicInternalInterface::class, $serviceLocator);
        
        return $serviceLocator;
    }
    
    private function defineDescriptorRepository(SystemEnvironmentInterface $systemEnvironment): DescriptorRepositoryInterface
    {
        $descriptorRepository       = $systemEnvironment->findDependency(DescriptorRepositoryInterface::class);
        
        if($descriptorRepository !== null) {
            return $descriptorRepository;
        }
        
        $descriptorRepository       = new DescriptorRepository(
            $this->defineRepositoryReader($systemEnvironment),
            new ContainerTypeResolver,
            $this->defineDescriptorBuilder($systemEnvironment)
        );
        
        $systemEnvironment->set(DescriptorRepositoryInterface::class, $descriptorRepository);
        
        return $descriptorRepository;
    }
    
    /**
     * @throws \Exception
     */
    private function defineRepositoryReader(SystemEnvironmentInterface $systemEnvironment): RepositoryReaderInterface
    {
        $reader                     = $systemEnvironment->findDependency(RepositoryReaderByScopeInterface::class);
        
        if($reader === null) {
            $reader                 = $systemEnvironment->findDependency(RepositoryReaderInterface::class);
        }
        
        if($reader === null) {
            throw new \Exception('RepositoryReaderInterface is required for ServiceManagerBootloader');
        }
        
        return $reader;
    }
    
    private function defineDescriptorBuilder(SystemEnvironmentInterface $systemEnvironment): ServiceDescriptorBuilderInterface
    {
        $descriptorBuilder          = $systemEnvironment->findDependency(ServiceDescriptorBuilderInterface::class);
        
        if($descriptorBuilder !== null) {
            return $descriptorBuilder;
        }
        
        $descriptorBuilder          = new ServiceDescriptorBuilderByReflection;
        
        $systemEnvironment->set(ServiceDescriptorBuilderInterface::class, $descriptorBuilder);
        
        return $descriptorBuilder;
    }
    
    #[\Override]
    public function dispose(): void
    {
        $this->systemEnvironment = null;
    }
}