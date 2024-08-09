<?php
declare(strict_types=1);

namespace IfCastle\Application\Bootloader\Builder;

use IfCastle\Application\Bootloader\BootManager\BootManagerApplication;
use IfCastle\DI\ConfigInterface;

final class BootloaderBuilderByDirectory extends BootloaderBuilderAbstract
{
    public function __construct(
        protected readonly string $appDirectory,
        private readonly string   $bootloaderDir,
        protected readonly string $applicationType
    ) {}
    
    #[\Override]
    protected function initConfigurator(): ConfigInterface
    {
        $configuratorFile           = $this->bootloaderDir.'/'.BootManagerApplication::CONFIGURATOR.'.ini';
        
        if(false === file_exists($configuratorFile)) {
            throw new \RuntimeException('Configurator file not found: ' . $configuratorFile);
        }
        
        $bootloaderConfig           = $this->read($configuratorFile);
        
        if(empty($bootloaderConfig['bootloader'])) {
            throw new \RuntimeException('Bootloader not found in configurator file: ' . $configuratorFile);
        }
        
        $configuratorClass          = $bootloaderConfig['bootloader'];
        
        if(false === class_exists($configuratorClass)) {
            throw new \RuntimeException('Configurator class not found: ' . $configuratorClass);
        }
        
        $configurator              = new $configuratorClass;
        
        if(false === $configurator instanceof ConfigInterface) {
            throw new \RuntimeException('Configurator class must implement ConfigInterface: ' . $configuratorClass);
        }
        
        if($configurator instanceof ZeroContextRequiredInterface) {
            $configurator->setZeroContext($this);
        }
        
        return $configurator;
    }
    
    protected function fetchBootloaders(): iterable
    {
        foreach (glob($this->bootloaderDir.'/*.ini') as $file) {
            
            if(str_ends_with('configurator.ini', $file)) {
                continue;
            }
            
            $bootloaderConfig        = $this->read($file);
            
            if(null === $bootloaderConfig) {
                continue;
            }
            
            if(array_key_exists('is_active', $bootloaderConfig) === false || empty($bootloaderConfig['is_active'])) {
                continue;
            }
            
            if(array_key_exists('for_application', $bootloaderConfig)
               && is_array($bootloaderConfig['for_application'])
               && in_array($this->applicationType, $bootloaderConfig['for_application']) === false) {
                continue;
            }
            
            if(array_key_exists('bootloader', $bootloaderConfig) === false || empty($bootloaderConfig['bootloader'])) {
                continue;
            }
            
            foreach ($bootloaderConfig['bootloader'] as $bootloaderClass) {
                yield $bootloaderClass;
            }
        }
    }
    
    protected function read(string $file): array|null
    {
        $data                       = parse_ini_file($file, true);
        
        if(false === is_array($data)) {
            return null;
        }
        
        return $data;
    }
}