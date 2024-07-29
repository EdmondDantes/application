<?php
declare(strict_types=1);

namespace IfCastle\Application\Bootloader\Builder;

final class BootloaderBuilderByDirectory extends BootloaderBuilderAbstract
{
    public function __construct(private readonly string $bootloaderDir) {}
    
    protected function fetchBootloaders(): iterable
    {
        foreach (glob($this->bootloaderDir.'/*.ini') as $file) {
            
            $bootloaderConfig       = parse_ini_file($file);
            
            if(false === is_array($bootloaderConfig)) {
                continue;
            }
            
            foreach ($bootloaderConfig as $bootloaderClass) {
                yield $bootloaderClass;
            }
        }
    }
}