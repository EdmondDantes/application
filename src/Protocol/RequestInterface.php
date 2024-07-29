<?php
declare(strict_types=1);

namespace IfCastle\Application\Protocol;

interface RequestInterface
{
    public function getRequestContext(): RequestContextI;
    public function getRequestContextParameters(): array;
}