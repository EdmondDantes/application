<?php
declare(strict_types=1);

namespace IfCastle\Application\RequestEnvironment;

use IfCastle\DesignPatterns\ExecutionPlan\ExecutionPlan;
use IfCastle\DesignPatterns\ExecutionPlan\SequentialPlanExecutorWithFinal;
use IfCastle\DesignPatterns\ExecutionPlan\WeakStaticClosureExecutor;

class RequestPlan                   extends ExecutionPlan
                                    implements RequestPlanInterface
{
    public function __construct()
    {
        $stages                     = [
            self::BUILD,
            self::BEFORE_DISPATCH,
            self::DISPATCH,
            self::BEFORE_HANDLE,
            self::EXECUTE,
            self::RESPONSE,
            self::AFTER_RESPONSE,
            self::FINALLY
        ];
        
        parent::__construct(
            new WeakStaticClosureExecutor(
                static fn(self $self, mixed $handler, RequestEnvironmentInterface $requestEnvironment)
                        => $self->executeHandler($handler, $requestEnvironment), $this
            ),
            $stages,
            new SequentialPlanExecutorWithFinal
        );
    }
    
    protected function executeHandler(mixed $handler, RequestEnvironmentInterface $requestEnvironment): void
    {
        if(is_callable($handler)) {
            $handler($requestEnvironment);
        }
    }
}