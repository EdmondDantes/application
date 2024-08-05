<?php
declare(strict_types=1);

namespace IfCastle\Application\Bootloader\Builder;

final class BootloaderBuilderByDirectory extends BootloaderBuilderAbstract
{
    public function __construct(private readonly string $bootloaderDir, protected string $appCode) {}
    
    protected function fetchBootloaders(): iterable
    {
        foreach (glob($this->bootloaderDir.'/*.ini') as $file) {
            
            $bootloaderConfig        = $this->convertIniToArray($file);
            
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
    
    protected function convertIniToArray(string $file): array|null
    {
        $data                       = parse_ini_file($file, true);
        
        if(false === is_array($data)) {
            return null;
        }
        
        $result                     = [];
        
        // Convert all sections with dot notation to nest arrays
        foreach ($data as $section => $values) {
            
            $parts                  = explode('.', $section);
            
            if(count($parts) === 1) {
                $result[$section]   = $values;
                continue;
            }
            
            $pointer                = &$result;
            
            foreach ($parts as $part) {
                
                if(array_key_exists($part, $pointer) === false) {
                    $pointer[$part] = [];
                }
                
                $pointer            = &$pointer[$part];
            }
            
            $pointer                = array_merge($pointer, $values);
        }
        
        return $result;
    }
}