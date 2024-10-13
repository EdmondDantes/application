<?php
declare(strict_types=1);

namespace IfCastle\Application\RequestEnvironment;

use IfCastle\DesignPatterns\ExecutionPlan\ExecutionPlanInterface;

interface RequestPlanInterface              extends ExecutionPlanInterface
{
    public const string BUILD               = 'b';
    public const string BEFORE_DISPATCH     = '-d';
    public const string DISPATCH            = 'd';
    public const string BEFORE_HANDLE       = '-e';
    public const string EXECUTE             = 'e';
    public const string RESPONSE            = 'r';
    public const string AFTER_RESPONSE      = '+r';
    public const string FINALLY             = 'f';
}