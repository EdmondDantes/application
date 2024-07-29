<?php
declare(strict_types=1);

namespace IfCastle\Application\Protocol;

interface RequestHandlerInterface
{
    public function handleRequest(RequestInterface $request, ResponseFactoryInterface $responseFactory): ResponseInterface;
}