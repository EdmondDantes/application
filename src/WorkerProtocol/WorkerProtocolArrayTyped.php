<?php

declare(strict_types=1);

namespace IfCastle\Application\WorkerProtocol;

use IfCastle\Application\Environment\SystemEnvironmentInterface;
use IfCastle\Application\WorkerProtocol\Exceptions\WorkerCommunicationException;
use IfCastle\DesignPatterns\Interceptor\InterceptorPipeline;
use IfCastle\DesignPatterns\Interceptor\InterceptorRegistryInterface;
use IfCastle\ServiceManager\CommandDescriptorInterface;
use IfCastle\ServiceManager\ExecutionContextInterface;
use IfCastle\TypeDefinitions\DefinitionAwareInterface;
use IfCastle\TypeDefinitions\NativeSerialization\ArraySerializableInterface;
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
        /* @phpstan-ignore-next-line */
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
        
        if($service instanceof CommandDescriptorInterface) {
            
            if($service instanceof ArraySerializableInterface) {
                $service            = ArrayTyped::serialize($service);
            } else {
                throw new WorkerCommunicationException(
                    'The worker request service is invalid: expected ArraySerializableInterface, got ' . get_class($service)
                );
            }
        }
        
        $context                    = ArrayTyped::serialize($context);

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
        if ($this->isMsgPackExtensionLoaded) {
            try {
                $data = \msgpack_unpack($request);
            } catch (\Throwable $exception) {
                throw new WorkerCommunicationException('The msgpack decode error occurred: ' . $exception->getMessage(), 0, $exception);
            }
        } else {
            try {
                $data = \json_decode($request, true, 512, JSON_THROW_ON_ERROR);
            } catch (\JsonException $exception) {
                throw new WorkerCommunicationException('The json decode error occurred: ' . $exception->getMessage(), 0, $exception);
            }
        }

        if (\count($data) !== 4) {
            throw new WorkerCommunicationException('The worker request data is invalid: expected 4 elements, got ' . \count($data));
        }

        [$service, $command, $parameters, $context] = $data;

        if(is_array($service)) {
            
            try {
                $service                = ArrayTyped::unserialize($service);
            } catch (\Throwable $exception) {
                throw new WorkerCommunicationException('The worker request service is invalid: ' . $exception->getMessage(), 0, $exception);
            }
            
        } elseif (!is_string($service)) {
            throw new WorkerCommunicationException('The worker request service is invalid: expected string, got ' . \gettype($service));
        }
        
        if(!is_string($command) && !is_null($command)) {
            throw new WorkerCommunicationException('The worker request command is invalid: expected string, got ' . gettype($command));
        }
        
        if(!is_array($parameters)) {
            throw new WorkerCommunicationException('The worker request parameters is invalid: expected array, got ' . gettype($parameters));
        }
        
        if(!is_array($context)) {
            throw new WorkerCommunicationException('The worker request context is invalid: expected array, got ' . gettype($context));
        }
        
        $context                    = ArrayTyped::unserialize($context);
        
        /* @phpstan-ignore-next-line */
        if(false === $context instanceof ExecutionContextInterface) {
            throw new WorkerCommunicationException(
                'The worker request context is invalid: expected ExecutionContextInterface, got ' . get_debug_type($context)
            );
        }
        
        /* @phpstan-ignore-next-line */
        return new WorkerRequest(
            $service instanceof CommandDescriptorInterface ?
                $service : new Command($service, $command, $parameters),
            $context
        );
    }

    #[\Override]
    public function buildWorkerResponse(ContainerSerializableInterface|\Throwable $response): string|null
    {
        if($response instanceof DefinitionAwareInterface) {
        
        }
        
        
    }

    #[\Override]
    public function parseWorkerResponse(string $response): object|null|false
    {
        // TODO: Implement parseWorkerResponse() method.
    }
}
