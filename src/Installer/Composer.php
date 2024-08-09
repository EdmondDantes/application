<?php
declare(strict_types=1);

namespace IfCastle\Application\Installer;

use Composer\Script\Event;
use Composer\Installer\PackageEvent;

final class Composer
{
    public static function postPackageInstall(PackageEvent $event): void
    {
    
    }
    
    public static function postPackageUpdate(PackageEvent $event): void
    {
    
    }
    
    public static function postPackageUninstall(PackageEvent $event): void
    {
    
    }
}