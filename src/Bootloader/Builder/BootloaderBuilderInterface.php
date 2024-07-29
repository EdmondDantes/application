<?php
declare(strict_types=1);

namespace IfCastle\Application\Bootloader\Builder;

use IfCastle\Application\Bootloader\BootloaderExecutorInterface;

interface BootloaderBuilderInterface
{
    public function build(): void;
    public function getBootloader(): BootloaderExecutorInterface;
}