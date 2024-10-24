<?php
declare(strict_types=1);

namespace IfCastle\Application\Bootloader\BootManager;

use PHPUnit\Framework\TestCase;

class BootManagerByDirectoryTest extends TestCase
{
    private string $bootloaderDir;
    
    #[\Override]
    protected function setUp(): void
    {
        $this->bootloaderDir        = __DIR__.'/bootloader';
        
        if(is_dir($this->bootloaderDir)) {
            // remove all files
            $files = glob($this->bootloaderDir.'/*');
            
            foreach ($files as $file) {
                if(is_file($file)) {
                    unlink($file);
                }
            }
        }
        
        if(!is_dir($this->bootloaderDir)) {
            mkdir($this->bootloaderDir);
        }
    }
    
    public function testAddBootloader(): void
    {
        $componentName              = 'componentName';
        $bootloaders                = ['bootloader1', 'bootloader2'];
        $applications               = ['application1', 'application2'];
        $file                       = $this->bootloaderDir.'/'.$componentName.'.ini';
        
        $data                       = [
            'isActive'              => true,
            'bootloader'            => $bootloaders,
            'forApplication'        => $applications
        ];
        
        $bootManager = new BootManagerByDirectory($this->bootloaderDir);
        $bootManager->addBootloader($componentName, $bootloaders, $applications);
        
        $this->assertFileExists($file);
        $this->assertEquals($data, parse_ini_file($file, true));
    }

    public function testActivateBootloader(): void
    {
        
        $componentName              = 'componentName';
        $file                       = $this->bootloaderDir.'/'.$componentName.'.ini';
        
        $data                       = [
            'is_active' => false,
            'bootloader' => ['bootloader1', 'bootloader2'],
            'for_application' => ['application1', 'application2']
        ];
        
        $bootManager = new BootManagerByDirectory($this->bootloaderDir);
        file_put_contents($file, $this->generateBootloaderContent($data));
        
        $bootManager->activateBootloader($componentName);
        
        $data = parse_ini_file($file, true, INI_SCANNER_TYPED);
        
        $this->assertTrue($data['is_active']);
    }

    private function generateBootloaderContent(array $data): string
    {
        $content = '';
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $content .= "[$key]\n";
                foreach ($value as $item) {
                    $content .= "$item\n";
                }
            } else {
                $content .= "$key = $value\n";
            }
        }
        return $content;
    }
}
