<?php
declare(strict_types=1);

namespace IfCastle\Application\RequestEnvironment;

use IfCastle\DI\ContainerInterface;
use IfCastle\Protocol\RequestInterface;
use IfCastle\Protocol\ResponseFactoryInterface;
use IfCastle\Protocol\ResponseInterface;

interface RequestEnvironmentInterface extends ContainerInterface
{
    public function getRequest(): RequestInterface;
    
    public function getResponseFactory(): ResponseFactoryInterface;
    
    public function getResponse(): ResponseInterface|null;
    
    public function defineResponse(ResponseInterface $response): void;
    
    public function redefineResponse(ResponseInterface $response): void;
}