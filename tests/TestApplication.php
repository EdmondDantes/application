<?php
declare(strict_types=1);

namespace IfCastle\Application;

use IfCastle\Application\Bootloader\Builder\BootloaderBuilderInMemory;
use IfCastle\Application\Bootloader\Builder\BootloaderBuilderInterface;
use IfCastle\Application\Environment\SystemEnvironmentInterface;

final class TestApplication extends ApplicationAbstract
{
    public static function runTest(string $appDir, BootloaderBuilderInterface $bootloaderBuilder = null): void
    {
        $bootloaderBuilder          = $bootloaderBuilder ?? new BootloaderBuilderInMemory($appDir, 'test');
        
        $bootloaderBuilder->build();
        $bootloader                 = $bootloaderBuilder->getBootloader();
        
        unset($bootloaderBuilder);
        
        try {
            
            $app                    = null;
            
            $bootloader->defineStartApplicationHandler(function (SystemEnvironmentInterface $systemEnvironment) use($appDir, &$app) {
                $app                = new static($appDir, $systemEnvironment);
                $app->start();
            });
            
            try {
                $bootloader->executePlan();
            } finally {
                $bootloader->dispose();
                unset($bootloader);
            }
            
            // Start the engine
            $app->engineStart();
            
        } finally {
            $app?->end();
        }
    }
}