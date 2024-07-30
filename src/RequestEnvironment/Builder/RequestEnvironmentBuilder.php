<?php
declare(strict_types=1);

namespace IfCastle\Application\RequestEnvironment\Builder;

use IfCastle\DesignPatterns\ExecutionPlan\BeforeAfterExecutor;

class RequestEnvironmentBuilder     extends BeforeAfterExecutor
                                    implements RequestEnvironmentBuilderInterface
{
    public function __construct()
    {
        parent::__construct(new HandlerExecutor);
    }
}