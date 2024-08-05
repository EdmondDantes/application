<?php
declare(strict_types=1);

namespace IfCastle\Application\Bootloader\BootManager;

use IfCastle\OsUtilities\FileSystem\File;
use IfCastle\OsUtilities\Safe;

class BootManagerByDirectory        implements BootManagerInterface
{
    public function __construct(protected string $bootloaderDir) {}
    
    
    #[\Override]
    public function addBootloader(
        string $componentName,
        array  $bootloaders,
        array  $applications = []
    ): void
    {
        $this->validateComponent($componentName);
        
        $file                       = $this->bootloaderDir.'/'.$componentName.'.ini';
        
        if(file_exists($file)) {
            throw new \RuntimeException('Bootloader already exists: '.$componentName);
        }

        $data = <<<INI
is_active = true
INI;

        foreach ($bootloaders as $bootloader) {
            $data .= 'bootloader[] = "'.$bootloader.'"'.PHP_EOL;
        }

        foreach ($applications as $application) {
            $data .= 'for_application[] = "'.$application.'"'.PHP_EOL;
        }

        File::put($file, $data);
    }
    
    #[\Override]
    public function activateBootloader(string $componentName): void
    {
        $this->validateComponent($componentName);
        
        $file                       = $this->bootloaderDir.'/'.$componentName.'.ini';
        
        if(false === file_exists($file)) {
            throw new \RuntimeException('Bootloader not found: '.$componentName);
        }
        
        $data                       = parse_ini_file($file, true);
        
        if(false === is_array($data)) {
            throw new \RuntimeException('Invalid bootloader file: '.$file);
        }
        
        $data['is_active']          = true;
        
        File::put($file, $this->arrayToIni($data));
    }
    
    #[\Override]
    public function deactivateBootloader(string $componentName): void
    {
        $this->validateComponent($componentName);
        
        $file                       = $this->bootloaderDir.'/'.$componentName.'.ini';
        
        if(false === file_exists($file)) {
            throw new \RuntimeException('Bootloader not found: '.$componentName);
        }
        
        $data                       = parse_ini_file($file, true);
        
        if(false === is_array($data)) {
            throw new \RuntimeException('Invalid bootloader file: '.$file);
        }
        
        $data['is_active']          = false;
        
        File::put($file, $this->arrayToIni($data));
    }
    
    #[\Override]
    public function removeBootloader(string $componentName): void
    {
        $this->validateComponent($componentName);
        
        $file                       = $this->bootloaderDir.'/'.$componentName.'.ini';
        
        if(false === file_exists($file)) {
            throw new \RuntimeException('Bootloader not found: '.$componentName);
        }
        
        Safe::execute(fn() => unlink($file));
    }
    
    protected function validateComponent(string $componentName): void
    {
        if(preg_match('/[^a-z0-9_]/', $componentName)) {
            throw new \RuntimeException('Invalid component name: '.$componentName);
        }
    }
    
    protected function arrayToIni(array $data): string
    {
        $ini                        = '';
        
        foreach ($data as $key => $value) {
            if(is_array($value)) {
                foreach ($value as $subValue) {
                    $ini .= $key.'[] = '.$this->valueToIni($subValue).PHP_EOL;
                }
                
                $ini .= PHP_EOL;
            } else {
                $ini .= $key.' = '.$this->valueToIni($value).PHP_EOL;
            }
        }
        
        return $ini;
    }
    
    protected function valueToIni(mixed $value): string
    {
        if(is_int($value) || is_float($value)) {
            return (string)$value;
        }
        
        if(is_bool($value)) {
            return $value ? 'true' : 'false';
        }
        
        return '"'.$value.'"';
    }
    
}