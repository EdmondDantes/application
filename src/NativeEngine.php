<?php
declare(strict_types=1);

namespace IfCastle\Application;

class NativeEngine                  extends EngineAbstract
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