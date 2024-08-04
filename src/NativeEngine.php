<?php
declare(strict_types=1);

namespace IfCastle\Application;

class NativeEngine                  implements EngineInterface
{
    #[\Override]
    public function start(): void
    {
    }
    
    #[\Override]
    public function getEngineName(): string
    {
        if($this->isServer()) {
            return 'php-cgi/'.phpversion();
        } else {
            return 'php-cli/'.phpversion();
        }
    }
    
    #[\Override]
    public function getEngineRole(): EngineRolesEnum
    {
        return $this->isServer() ? EngineRolesEnum::SERVER : EngineRolesEnum::CONSOLE;
    }
    
    #[\Override]
    public function isServer(): bool
    {
        return \php_sapi_name() !== 'cli';
    }
    
    #[\Override]
    public function isProcess(): bool
    {
        return false;
    }
    
    #[\Override]
    public function isConsole(): bool
    {
        return \php_sapi_name() === 'cli';
    }
    
    #[\Override]
    public function isStateful(): bool
    {
        return false;
    }
    
    #[\Override]
    public function isAsynchronous(): bool
    {
        return false;
    }
    
    #[\Override]
    public function supportCoroutines(): bool
    {
        return false;
    }
}