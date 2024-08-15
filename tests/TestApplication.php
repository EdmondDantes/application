<?php
declare(strict_types=1);

namespace IfCastle\Application;

use IfCastle\Application\Bootloader\Builder\BootloaderBuilderInMemory;
use IfCastle\Application\Bootloader\Builder\BootloaderBuilderInterface;

final class TestApplication extends ApplicationAbstract
{
    #[\Override]
    protected static function defineBootloader(string $appDir): BootloaderBuilderInterface
    {
        return new BootloaderBuilderInMemory($appDir, 'test');
    }
}