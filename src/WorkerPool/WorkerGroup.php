<?php
declare(strict_types=1);

namespace IfCastle\Application\WorkerPool;

final readonly class WorkerGroup implements WorkerGroupInterface
{
    public function __construct(
        public string                   $entryPointClass,
        public WorkerTypeEnum           $workerType,
        public int                      $minWorkers      = 0,
        public int                      $maxWorkers      = 0,
        public string                   $groupName       = ''
    ) {}
}