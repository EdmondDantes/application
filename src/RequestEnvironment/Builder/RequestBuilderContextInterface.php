<?php
declare(strict_types=1);

namespace IfCastle\Application\RequestEnvironment\Builder;

interface RequestBuilderContextInterface
{
    public function getOriginalRequest(): object|null;
    
    public function getRequest(): object|null;
    
    public function setRequest(object $request): static;
}