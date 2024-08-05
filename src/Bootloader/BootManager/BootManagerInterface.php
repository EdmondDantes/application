<?php
declare(strict_types=1);

namespace IfCastle\Application\Bootloader\BootManager;

interface BootManagerInterface
{
    public function addBootloader(string $componentName, array $bootloaders, array $applications = []): void;
    
    public function activateBootloader(string $componentName): void;
    
    public function deactivateBootloader(string $componentName): void;
    
    public function removeBootloader(string $componentName): void;
}