<?php

declare(strict_types=1);

namespace IfCastle\Application;

use IfCastle\Application\Environment\SystemEnvironmentInterface;
use IfCastle\DI\DisposableInterface;
use IfCastle\Exceptions\BaseException;
use IfCastle\Exceptions\BaseExceptionInterface;
use IfCastle\Exceptions\Errors\Error;
use IfCastle\Exceptions\FatalException;
use IfCastle\OsUtilities\SystemClock\SystemClock;
use Psr\Log\LoggerInterface;

abstract class ApplicationAbstract implements ApplicationInterface
{
    public const string APP_CODE    = 'app';

    protected LoggerInterface|null $logger = null;

    protected array $afterEngineHandlers = [];

    private bool $isStarted         = false;

    private bool $isEnded           = false;

    private int $startTime          = 0;

    private int $endTime            = 0;

    private string $vendorDir       = '';

    private static string $reservedMemory = '';

    public function __construct(protected readonly string                    $appDir,
        protected readonly SystemEnvironmentInterface $systemEnvironment
    ) {}

    /**
     * @throws BaseException
     */
    #[\Override]
    final public function start(): void
    {
        if ($this->isStarted) {
            return;
        }

        $this->isStarted            = true;
        $this->startTime            = (new SystemClock())->now();
        $this->vendorDir            = $this->appDir . '/vendor';

        if (false === \is_dir($this->vendorDir)) {
            throw new BaseException('vendor dir undefined');
        }

        if (self::$reservedMemory === '') {
            self::$reservedMemory   = \str_repeat('x', $this->getReservedMemorySize());
        }

        \register_shutdown_function(function () {

            self::$reservedMemory   = '';
            $error                  = \error_get_last();

            if ($this->isEnded && $error === null) {
                return;
            }

            if (!(isset($error['type'])
                  && (\E_ERROR | \E_PARSE | \E_CORE_ERROR | \E_CORE_WARNING | \E_COMPILE_ERROR | \E_COMPILE_WARNING))) {
                return;
            }

            $error                  = Error::createFromLastError($error);

            if ($this->endTime === 0) {
                $this->endTime      = (new SystemClock())->now();
            }

            $this->unexpectedShutdownHandler($error);
        });

        try {
            $this->logger           = $this->systemEnvironment->findDependency(LoggerInterface::class);
        } catch (\Throwable $throwable) {
            $this->logger?->critical(new FatalException('Application init error', 0, $throwable));
            $this->criticalLog($throwable);
        }
    }

    #[\Override]
    public function defineAfterEngineHandlers(array $afterEngineHandlers): void
    {
        $this->afterEngineHandlers  = $afterEngineHandlers;
    }

    #[\Override]
    public function engineStart(): void
    {
        try {

            $engine                 = $this->defineEngine();

            if ($engine === null) {
                throw new FatalException('Engine is not found');
            }

            $engine->defineEngineRole($this->defineEngineRole());

            $this->engineStartBefore();

            foreach ($this->afterEngineHandlers as $handler) {
                $handler($this->systemEnvironment, $engine);
            }

            $engine->start();
            $this->engineStartAfter();

        } catch (\Throwable $throwable) {
            $this->logger?->critical(new FatalException('Application init error', 0, $throwable));
            $this->criticalLog($throwable);
        }
    }

    protected function engineStartBefore(): void {}

    protected function engineStartAfter(): void {}

    protected function defineEngine(): EngineInterface|null
    {
        return $this->systemEnvironment->findDependency(EngineInterface::class);
    }

    abstract protected function defineEngineRole(): EngineRolesEnum;

    #[\Override]
    final public function end(): void
    {
        if ($this->isEnded) {
            return;
        }

        $this->isEnded              = true;
        $this->endTime              = (new SystemClock())->now();

        if (isset($this->engine)) {
            $this->engine->free();
        }

        $this->systemEnvironment->dispose();

        if ($this->logger instanceof DisposableInterface) {
            $this->logger->dispose();
        }

        $this->logger               = null;
    }

    #[\Override]
    public function getEngine(): EngineInterface
    {
        return $this->systemEnvironment->resolveDependency(EngineInterface::class);
    }

    #[\Override]
    public function getStartTime(): int
    {
        return $this->startTime;
    }

    #[\Override]
    public function getAppDir(): string
    {
        return $this->appDir;
    }

    #[\Override]
    public function getVendorDir(): string
    {
        return $this->vendorDir;
    }

    #[\Override]
    public function getServerName(): string
    {
        return '';
    }

    #[\Override]
    public function isDeveloperMode(): bool
    {
        return $this->systemEnvironment->isDeveloperMode();
    }

    public function criticalLog(mixed $data): void
    {
        if (!\is_dir($this->appDir . '/logs')) {
            \mkdir($this->appDir . '/logs');
        }

        if (!\is_dir($this->appDir . '/logs') || !\is_writable($this->appDir . '/logs')) {
            $dir                    = \sys_get_temp_dir();
        } else {
            $dir                    = $this->appDir . '/logs';
        }

        \file_put_contents($dir . '/critical.log', "\n---\n" . \print_r((string) $data, true), FILE_APPEND);
    }

    protected function getReservedMemorySize(): int
    {
        // 10kb
        return 10240;
    }

    protected function unexpectedShutdownHandler(BaseExceptionInterface $error): void
    {
        $this->criticalLog($error);
        $this->logger?->error($error);

        if ($this->logger instanceof DisposableInterface) {
            $this->logger->dispose();
        }

        // Try to end the system
        $this->end();
    }
}
