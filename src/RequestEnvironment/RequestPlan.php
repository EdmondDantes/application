<?php

declare(strict_types=1);

namespace IfCastle\Application\RequestEnvironment;

use IfCastle\DesignPatterns\ExecutionPlan\ExecutionPlan;
use IfCastle\DesignPatterns\ExecutionPlan\PlanExecutorWithFinalAndStageControl;
use IfCastle\DesignPatterns\ExecutionPlan\StagePointer;
use IfCastle\DesignPatterns\ExecutionPlan\WeakStaticClosureExecutor;
use IfCastle\Exceptions\ClientAvailableInterface;
use IfCastle\Exceptions\ClientException;
use IfCastle\Protocol\Exceptions\ParseException;

class RequestPlan extends ExecutionPlan implements RequestPlanInterface
{
    public function __construct()
    {
        parent::__construct(
            new WeakStaticClosureExecutor(
                static fn(self $self, mixed $handler, RequestEnvironmentInterface $requestEnvironment)
                        => $self->executeHandler($handler, $requestEnvironment), $this
            ),
            [
                self::RAW_BUILD,
                self::BUILD,
                self::BEFORE_DISPATCH,
                self::DISPATCH,
                self::BEFORE_EXECUTE,
                self::EXECUTE,
                self::RESPONSE,
                self::AFTER_RESPONSE,
                self::FINALLY,
            ],
            new PlanExecutorWithFinalAndStageControl()
        );
    }

    #[\Override]
    public function addRawBuildHandler(callable $handler): static
    {
        return $this->addStageHandler(self::RAW_BUILD, $handler);
    }

    #[\Override]
    public function addBuildHandler(callable $handler): static
    {
        return $this->addStageHandler(self::BUILD, $handler);
    }

    #[\Override]
    public function addBeforeDispatchHandler(callable $handler): static
    {
        return $this->addStageHandler(self::BEFORE_DISPATCH, $handler);
    }

    #[\Override]
    public function addDispatchHandler(callable $handler): static
    {
        return $this->addStageHandler(self::DISPATCH, $handler);
    }

    #[\Override]
    public function addBeforeHandleHandler(callable $handler): static
    {
        return $this->addStageHandler(self::BEFORE_EXECUTE, $handler);
    }

    #[\Override]
    public function addExecuteHandler(callable $handler): static
    {
        return $this->addStageHandler(self::EXECUTE, $handler);
    }

    #[\Override]
    public function addResponseHandler(callable $handler): static
    {
        return $this->addStageHandler(self::RESPONSE, $handler);
    }

    #[\Override]
    public function addAfterResponseHandler(callable $handler): static
    {
        return $this->addStageHandler(self::AFTER_RESPONSE, $handler);
    }

    #[\Override]
    public function addFinallyHandler(callable $handler): static
    {
        return $this->addStageHandler(self::FINALLY, $handler);
    }

    protected function executeHandler(mixed $handler, RequestEnvironmentInterface $requestEnvironment): StagePointer|null
    {
        if (false === \is_callable($handler)) {
            return null;
        }

        try {
            $result                 = $handler($requestEnvironment);

            if ($result instanceof StagePointer) {
                return $result;
            }

        } catch (ParseException|ClientAvailableInterface $exception) {

            if ($exception instanceof ParseException) {
                $exception          = new ClientException('Failed to parse request', [], ['exception' => $exception]);
            }

            return $this->setClientExceptionAsResponse($exception, $requestEnvironment);
        } catch (\Throwable $exception) {
            return $this->setServerExceptionAsResponse($exception, $requestEnvironment);
        }

        return null;
    }

    protected function setClientExceptionAsResponse(ClientAvailableInterface & \Throwable $exception, RequestEnvironmentInterface $requestEnvironment): StagePointer
    {
        $requestEnvironment->getResponseFactory()->createFailedResponse($exception);
    }

    protected function setServerExceptionAsResponse(\Throwable $exception, RequestEnvironmentInterface $requestEnvironment): StagePointer
    {
        $requestEnvironment->getResponseFactory()->createFailedResponse($exception);
    }
}
