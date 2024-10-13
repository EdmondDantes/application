<?php
declare(strict_types=1);

namespace IfCastle\Application\RequestEnvironment\Builder;

class RequestBuilderContext implements RequestBuilderContextInterface
{
    public function __construct(protected object|null $originalRequest = null, protected object|null $result = null) {}
    
    #[\Override]
    public function getOriginalRequest(): object|null
    {
        return $this->originalRequest;
    }
    
    #[\Override]
    public function getRequest(): object|null
    {
        return $this->result;
    }
    
    #[\Override] public function setRequest(object $request): static
    {
        $this->result               = $request;
        return $this;
    }
}