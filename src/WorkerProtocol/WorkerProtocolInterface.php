<?php

declare(strict_types=1);

namespace IfCastle\Application\WorkerProtocol;

use IfCastle\Application\WorkerProtocol\Exceptions\WorkerCommunicationException;
use IfCastle\ServiceManager\CommandDescriptorInterface;
use IfCastle\ServiceManager\ExecutionContextInterface;
use IfCastle\TypeDefinitions\Value\ContainerSerializableInterface;

interface WorkerProtocolInterface
{
    /**
     * @param array<string, mixed> $parameters
     *
     * @throws WorkerCommunicationException
     */
    public function buildWorkerRequest(
        string|CommandDescriptorInterface  $service,
        ?string                            $command      = null,
        array                              $parameters   = [],
        ?ExecutionContextInterface         $context      = null
    ): string;

    /**
     * @throws WorkerCommunicationException
     */
    public function parseWorkerRequest(string|array $request): WorkerRequestInterface;

    public function buildWorkerResponse(ContainerSerializableInterface|\Throwable $response): string|null;

    /**
     * @throws WorkerCommunicationException
     */
    public function parseWorkerResponse(string $response): object|null|false;
}
