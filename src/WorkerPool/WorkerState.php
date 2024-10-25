<?php

declare(strict_types=1);

namespace IfCastle\Application\WorkerPool;

readonly class WorkerState implements WorkerStateInterface
{
    public function __construct(
        public int  $workerId,
        public int  $groupId,
        public bool $shouldBeStarted,
        public int  $pid = 0
    ) {}

    public function getWorkerId(): int
    {
        return $this->workerId;
    }

    public function getGroupId(): int
    {
        return $this->groupId;
    }

    public function isShouldBeStarted(): bool
    {
        return $this->shouldBeStarted;
    }

    public function getPid(): int
    {
        return $this->pid;
    }
}
