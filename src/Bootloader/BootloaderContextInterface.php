<?php
declare(strict_types=1);

namespace IfCastle\Application\Bootloader;

use IfCastle\Application\Bootloader\Builder\PublicEnvironmentBuilderInterface;
use IfCastle\Application\RequestEnvironment\Builder\RequestEnvironmentBuilderInterface;
use IfCastle\DI\BuilderInterface;
use IfCastle\DI\ContainerInterface;

interface BootloaderContextInterface extends ContainerInterface
{
    public function getSystemEnvironmentBootBuilder(): BuilderInterface;
    
    public function getPublicEnvironmentBootBuilder(): PublicEnvironmentBuilderInterface;
    
    public function getRequestEnvironmentBuilder(): RequestEnvironmentBuilderInterface;
}