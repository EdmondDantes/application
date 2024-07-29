<?php
declare(strict_types=1);

namespace IfCastle\Application\Protocol;

interface RequestFactoryInterface
{
    public function createRequest(): RequestInterface;
}