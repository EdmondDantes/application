<?php

declare(strict_types=1);

namespace IfCastle\Application\Console;

use Psr\Log\LoggerInterface;

interface ConsoleLoggerInterface extends LoggerInterface
{
    public const string PID         = 'pid';

    public const string WORKER      = 'worker';

    public const string STATUS      = 'status';

    public const string IS_FAILURE  = 'isFailure';

    public const string NO_TIMESTAMP = 'noTimestamp';

    public const string IN_FRAME    = 'inFrame';

    public const string VERSION     = 'version';
}
