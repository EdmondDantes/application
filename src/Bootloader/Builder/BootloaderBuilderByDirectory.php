<?php
declare(strict_types=1);

namespace IfCastle\Application\Bootloader\Builder;

final class BootloaderBuilderByDirectory extends BootloaderBuilderAbstract
{
    public function __construct(private readonly string $bootloaderDir, protected string $appCode) {}
    
    protected function fetchBootloaders(): iterable
    {
        foreach (glob($this->bootloaderDir.'/*.ini') as $file) {
            
            $bootloaderConfig        = $this->read($file);
            
            if(null === $bootloaderConfig) {
                continue;
            }
            
            if(array_key_exists('is_active', $bootloaderConfig) === false || empty($bootloaderConfig['is_active'])) {
                continue;
            }
            
            if(array_key_exists('for_application', $bootloaderConfig)
               && is_array($bootloaderConfig['for_application'])
               && in_array($this->appCode, $bootloaderConfig['for_application']) === false) {
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