<?php
declare(strict_types=1);

namespace IfCastle\Application\Bootloader\BootManager;

class BootManagerApplication
{
    public static function run(string $appDir, array $command = null): never
    {
        $appDir                     = $appDir.'/bootloader';
        $manager                    = new BootManagerByDirectory($appDir);
        
        if($command === null) {
            echo 'No command specified. Exiting...'.PHP_EOL;
            exit(0);
        }
        
        if(empty($command['action'])) {
            echo 'No command action specified. Exiting...'.PHP_EOL;
            exit(1);
        }
        
        switch ($command['action']) {
            case 'add':
                self::add($manager, $command);
                break;
            case 'activate':
                self::activate($manager, $command);
                break;
            case 'disable':
                self::disable($manager, $command);
                break;
            case 'remove':
                self::remove($manager, $command);
                break;
            default:
                echo 'Invalid command action specified. Exiting...'.PHP_EOL;
                exit(2);
        }
        
        exit();
    }
    
    public static function add(BootManagerInterface $bootManager, array $command): void
    {
        foreach (['component', 'bootloaders'] as $key) {
            if(empty($command[$key])) {
                echo 'Missing required parameter: '.$key.PHP_EOL;
                exit(3);
            }
        }
        
        $bootManager->addBootloader($command['component'], $command['bootloaders'], $command['applications'] ?? []);
    }
    
    public static function activate(BootManagerInterface $bootManager, array $command): void
    {
        if(empty($command['component'])) {
            echo 'Missing required parameter: component'.PHP_EOL;
            exit(4);
        }
        
        $bootManager->activateBootloader($command['component']);
    }
    
    public static function disable(BootManagerInterface $bootManager, array $command): void
    {
        if(empty($command['component'])) {
            echo 'Missing required parameter: component'.PHP_EOL;
            exit(5);
        }
        
        $bootManager->deactivateBootloader($command['component']);
    }
    
    public static function remove(BootManagerInterface $bootManager, array $command): void
    {
        if(empty($command['component'])) {
            echo 'Missing required parameter: component'.PHP_EOL;
            exit(6);
        }
        
        $bootManager->removeBootloader($command['component']);
    }
}