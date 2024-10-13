<?php
declare(strict_types=1);

namespace IfCastle\Application\RequestEnvironment\Builder;

use IfCastle\Application\RequestEnvironment\RequestEnvironmentInterface;

class RequestBuilderContext implements RequestBuilderContextInterface
{
    protected RequestEnvironmentInterface $requestEnvironment;
    
    public function __construct(protected object|null $originalRequest = null)
    {
        $this->requestEnvironment = new RequestEnvironment();
    }
    
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
    
    #[\Override]
    public function setRequest(object $request): static
    {
        $this->result               = $request;
        return $this;
    }
}