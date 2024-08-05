<?php
declare(strict_types=1);

namespace IfCastle\Application\Bootloader\Builder;

use IfCastle\Application\Bootloader\BootloaderExecutor;
use IfCastle\Application\Bootloader\BootloaderExecutorInterface;
use IfCastle\Application\Bootloader\BootloaderInterface;

abstract class BootloaderBuilderAbstract implements BootloaderBuilderInterface
{
    protected BootloaderExecutorInterface|null $bootloader = null;
    
    public function build(): void
    {
        if($this->bootloader === null) {
            $this->bootloader        = new BootloaderExecutor();
        }
        
        foreach ($this->fetchBootloaders() as $bootloaderClass) {
            if(false === class_exists($bootloaderClass)) {
                throw new \RuntimeException('Bootloader class not found: ' . $bootloaderClass);
            }
            
            $this->handleBootloaderClass($bootloaderClass);
        }
    }
    
    public function getBootloader(): BootloaderExecutorInterface
    {
        if($this->bootloader === null) {
            throw new \RuntimeException('Bootloader not built');
        }
        
        return $this->bootloader;
    }
    
    abstract protected function fetchBootloaders(): iterable;
    
    protected function handleBootloaderClass(string $bootloaderClass): void
    {
        $object                     = new $bootloaderClass();
        
        if(false === $object instanceof BootloaderInterface) {
            throw new \RuntimeException('Bootloader class must implement BootloaderInterface: ' . $bootloaderClass);
        }
        
        $object->buildBootloader($this->bootloader);
    }
}