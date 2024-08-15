<?php
declare(strict_types=1);

namespace IfCastle\Application\MockApplication;

use IfCastle\Application\Bootloader\BootloaderExecutorInterface;
use IfCastle\Application\Bootloader\BootloaderInterface;
use IfCastle\Application\Bootloader\ServiceManager\ServiceManagerBootloader;
use IfCastle\Application\EngineInterface;
use IfCastle\Application\NativeEngine;
use Random\Engine;

final class TestBootloader implements BootloaderInterface
{
    #[\Override]
    public function buildBootloader(BootloaderExecutorInterface $bootloaderExecutor): void
    {
        // Off the service manager bootloader
        $bootloaderExecutor->getBootloaderContext()->set(ServiceManagerBootloader::WAS_SERVICE_MANAGER_BOOTLOADER_EXECUTED, true);
        $bootloaderExecutor->getBootloaderContext()->getSystemEnvironmentBootBuilder()
                                                   ->bindConstructible(EngineInterface::class, NativeEngine::class);
    }
}