<?php
declare(strict_types=1);

namespace IfCastle\Application;

use IfCastle\Application\Bootloader\Builder\BootloaderBuilderInterface;

abstract class ApplicationAbstract implements ApplicationInterface
{
    public static function run(string $appDir, BootloaderBuilderInterface $bootloaderBuilder): never
    {
        try {
            $bootloaderBuilder->build();
            $bootloader             = $bootloaderBuilder->getBootloader();
            $bootloader->executePlan();
        } catch (\Throwable $throwable) {
            //
        }
        
        
    }
    
    #[\Override]
    public function start(): void
    {
        // TODO: Implement start() method.
    }
    
    #[\Override]
    public function end(): void
    {
        // TODO: Implement end() method.
    }
    
    #[\Override]
    public function getEngine(): EngineInterface
    {
        // TODO: Implement getEngine() method.
    }
    
    #[\Override] public function getStartTime(): int
    {
        // TODO: Implement getStartTime() method.
    }
    
    #[\Override] public function getAppDir(): string
    {
        // TODO: Implement getAppDir() method.
    }
    
    #[\Override] public function getVendorDir(): string
    {
        // TODO: Implement getVendorDir() method.
    }
    
    #[\Override] public function getServerName(): string
    {
        // TODO: Implement getServerName() method.
    }
    
    #[\Override] public function isDeveloperMode(): bool
    {
        // TODO: Implement isDeveloperMode() method.
    }
}