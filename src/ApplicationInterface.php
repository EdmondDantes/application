<?php

declare(strict_types=1);

namespace IfCastle\Application;

interface ApplicationInterface
{
    public function start(): void;
    
    /**
     * @param array<callable> $afterEngineHandlers
     */
    public function defineAfterEngineHandlers(array $afterEngineHandlers): void;

    public function engineStart(): void;

    public function end(): void;

    public function getEngine(): EngineInterface;

    public function getStartTime(): int;

    public function getAppDir(): string;

    public function getVendorDir(): string;

    public function getServerName(): string;

    public function isDeveloperMode(): bool;
}
