<?php
declare(strict_types=1);

namespace IfCastle\Application\WorkerProtocol;

use IfCastle\Protocol\HeadersInterface;
use IfCastle\Protocol\RequestInterface;
use IfCastle\ServiceManager\CommandDescriptorInterface;
use IfCastle\ServiceManager\ExecutionContextInterface;

interface WorkerRequestInterface extends RequestInterface, HeadersInterface
{
    public function getCommandDescriptor(): CommandDescriptorInterface;
    
    public function getExecutionContext(): ExecutionContextInterface;
}