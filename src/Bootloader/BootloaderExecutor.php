<?php
declare(strict_types=1);

namespace IfCastle\Application\Bootloader;

use IfCastle\DesignPatterns\ExecutionPlan\BeforeAfterExecutor;

class BootloaderExecutor            extends BeforeAfterExecutor
                                    implements BootloaderExecutorInterface
{
    public function __construct()
    {
        parent::__construct();
    }
}