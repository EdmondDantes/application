<?php
declare(strict_types=1);

namespace IfCastle\Application\Bootloader\ServiceManager;

use IfCastle\Application\Environment\PublicEnvironmentInterface;
use IfCastle\Application\Environment\SystemEnvironmentInterface;
use IfCastle\DI\AutoResolverInterface;
use IfCastle\DI\ContainerInterface;
use IfCastle\DI\DisposableInterface;
use IfCastle\DI\ResolverInterface;
use IfCastle\ServiceManager\DescriptorRepository;
use IfCastle\ServiceManager\DescriptorRepositoryInterface;
use IfCastle\ServiceManager\ExecutorInterface;
use IfCastle\ServiceManager\RepositoryStorages\RepositoryReaderByScopeBridge;
use IfCastle\ServiceManager\RepositoryStorages\RepositoryReaderByScopeInterface;
use IfCastle\ServiceManager\RepositoryStorages\RepositoryReaderInterface;
use IfCastle\ServiceManager\ServiceDescriptorBuilderInterface;
use IfCastle\ServiceManager\ServiceLocator;
use IfCastle\ServiceManager\ServiceLocatorInterface;

final class ServiceManagerBootloader implements AutoResolverInterface, DisposableInterface
{
    protected SystemEnvironmentInterface|null $systemEnvironment = null;
    
    #[\Override]
    public function resolveDependencies(ContainerInterface $container): void
    {
        if($container instanceof SystemEnvironmentInterface) {
            $this->systemEnvironment = $container;
        }
    }
    
    #[\Override]
    public function dispose(): void
    {
        $this->systemEnvironment    = null;
    }
    
    public function __invoke(): void
    {
        if($this->systemEnvironment === null) {
            return;
        }
        
        $sysEnv                     = $this->systemEnvironment;
        
        $publicEnvironment          = $sysEnv->resolveDependency(PublicEnvironmentInterface::class);
        $reader                     = $sysEnv->resolveDependency(RepositoryReaderByScopeInterface::class);
        
        $publicReader               = new RepositoryReaderByScopeBridge($reader, $this->defineScopes());
        $internalReader             = new RepositoryReaderByScopeBridge($reader, []);
        
        $sysEnv->set(RepositoryReaderInterface::class, $internalReader);
        $publicEnvironment->set(RepositoryReaderInterface::class, $publicReader);
        
        if(false === $this->systemEnvironment->hasDependency(DescriptorRepositoryInterface::class)) {
            
            $descriptorRepository = new DescriptorRepository(
                $internalReader,
                $sysEnv->resolveDependency(ResolverInterface::class),
                $sysEnv->resolveDependency(ServiceDescriptorBuilderInterface::class)
            );
            
            $this->systemEnvironment->set(DescriptorRepositoryInterface::class, $descriptorRepository);
        }
        
        if(false === $publicEnvironment->hasDependency(DescriptorRepositoryInterface::class)) {
            $publicEnvironment->set(
                DescriptorRepositoryInterface::class,
                new DescriptorRepository(
                    $publicReader,
                    $publicEnvironment->resolveDependency(ResolverInterface::class),
                    $publicEnvironment->resolveDependency(ServiceDescriptorBuilderInterface::class)
                )
            );
        }
        
        if(false === $sysEnv->hasDependency(ServiceLocatorInterface::class)) {
            $sysEnv->set(
                ServiceLocatorInterface::class,
                new ServiceLocator($sysEnv->resolveDependency(DescriptorRepositoryInterface::class))
            );
        }
        
        if(false === $publicEnvironment->hasDependency(ServiceLocatorInterface::class)) {
            $publicEnvironment->set(
                ServiceLocatorInterface::class,
                new ServiceLocator($publicEnvironment->resolveDependency(DescriptorRepositoryInterface::class))
            );
        }
        
        if(false === $sysEnv->hasDependency(ExecutorInterface::class)) {
            $sysEnv->set(ExecutorInterface::class, new InternalExecutorInitializer);
        }
        
        if(false === $publicEnvironment->hasDependency(ExecutorInterface::class)) {
            $publicEnvironment->set(ExecutorInterface::class, new PublicExecutorInitializer);
        }
        
        $this->dispose();
    }
    
    protected function defineScopes(): array
    {
        /** @todo Implement defineScopes method */
        return [];
    }
}