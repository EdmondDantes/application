<?php
declare(strict_types=1);

namespace IfCastle\Application\RequestEnvironment\Builder;

use IfCastle\DesignPatterns\ExecutionPlan\BeforeAfterExecutor;
use IfCastle\DesignPatterns\ExecutionPlan\WeakStaticClosureExecutor;

class RequestEnvironmentBuilder     extends BeforeAfterExecutor
                                    implements RequestEnvironmentBuilderInterface
{
    public function __construct()
    {
        parent::__construct(new WeakStaticClosureExecutor(
            static fn(self $self, mixed $handler, RequestBuilderContextInterface $requestBuilderContext)
                    => $self->handle($handler, $requestBuilderContext), $this)
        );
    }
    
    public function handleRequest(RequestBuilderContextInterface $requestBuilderContext): void
    {
        $this->executePlan($requestBuilderContext);
    }
    
    protected function handle(mixed $handler, RequestBuilderContextInterface $requestBuilderContext): void
    {
        if(is_callable($handler)) {
            $handler($requestBuilderContext);
        }
    }
}