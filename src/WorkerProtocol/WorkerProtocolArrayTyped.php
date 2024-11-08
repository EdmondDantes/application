<?php

declare(strict_types=1);

namespace IfCastle\Application\WorkerProtocol;

use IfCastle\Application\Environment\SystemEnvironmentInterface;
use IfCastle\Application\WorkerProtocol\Exceptions\WorkerCommunicationException;
use IfCastle\DesignPatterns\Interceptor\InterceptorPipeline;
use IfCastle\DesignPatterns\Interceptor\InterceptorRegistryInterface;
use IfCastle\ServiceManager\CommandDescriptorInterface;
use IfCastle\ServiceManager\ExecutionContextInterface;
use IfCastle\TypeDefinitions\NativeSerialization\ArrayTyped;
use IfCastle\TypeDefinitions\Value\ContainerSerializableInterface;

final class WorkerProtocolArrayTyped implements WorkerProtocolInterface
{
    private ?bool $isMsgPackExtensionLoaded = null;

    /**
     * @var array<WorkerProtocolInterceptorInterface>
     */
    protected array $interceptors = [];

    public function __construct(
        protected SystemEnvironmentInterface $systemEnvironment,
        ?InterceptorRegistryInterface $interceptorRegistry = null
    ) {

        $this->isMsgPackExtensionLoaded = \extension_loaded('msgpack');
        $this->interceptors = $interceptorRegistry?->resolveInterceptors(WorkerProtocolInterceptorInterface::class)
                              ?? [];
    }

    #[\Override]
    public function buildWorkerRequest(
        string|CommandDescriptorInterface $service,
        ?string                           $command      = null,
        array                             $parameters   = [],
        ?ExecutionContextInterface        $context      = null
    ): string {
        [, $service, $command, $parameters, $context] = (new InterceptorPipeline(
            $this, [__METHOD__, $service, $command, $parameters, $context], ...$this->interceptors
        ))->getLastArguments();
        
        $context                    = $context?->toArray();

        // Serialize parameters
        foreach ($parameters as $key => $parameter) {
            if ($parameter instanceof ContainerSerializableInterface) {
                $parameters[$key]   = $parameter->containerToString();
            }
        }

        if ($this->isMsgPackExtensionLoaded) {
            try {
                return \msgpack_pack([$service, $command, $parameters, $context]);
            } catch (\Throwable $exception) {
                throw new WorkerCommunicationException('The msgpack encode error occurred: ' . $exception->getMessage(), 0, $exception);
            }
        }

        try {
            return \json_encode([$service, $command, $parameters, $context], JSON_THROW_ON_ERROR);
        } catch (\JsonException $exception) {
            throw new WorkerCommunicationException('The json encode error occurred: ' . $exception->getMessage(), 0, $exception);
        }
    }

    #[\Override]
    public function parseWorkerRequest(array|string $request): WorkerRequestInterface
    {
        // TODO: Implement parseWorkerRequest() method.
    }

    #[\Override] public function buildWorkerResponse(ContainerSerializableInterface|\Throwable $response): string|null
    {
        // TODO: Implement buildWorkerResponse() method.
    }

    #[\Override] public function parseWorkerResponse(string $response): object|null|false
    {
        // TODO: Implement parseWorkerResponse() method.
    }
}
