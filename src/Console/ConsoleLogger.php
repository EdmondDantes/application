<?php

declare(strict_types=1);

namespace IfCastle\Application\Console;

use Psr\Log\LoggerTrait;
use Psr\Log\LogLevel;

final readonly class ConsoleLogger implements ConsoleLoggerInterface
{
    use LoggerTrait;

    public function __construct(private ConsoleOutputInterface $consoleOutput) {}


    public function log($level, \Stringable|string $message, array $context = []): void
    {
        $verbosity                  = match ($level) {
            LogLevel::DEBUG                                          => ConsoleOutputInterface::VERBOSITY_DEBUG,
            LogLevel::ERROR                                          => ConsoleOutputInterface::VERBOSITY_VERBOSE,
            LogLevel::CRITICAL, LogLevel::ALERT, LogLevel::EMERGENCY => ConsoleOutputInterface::VERBOSITY_VERY_VERBOSE,
            default                                                  => ConsoleOutputInterface::VERBOSITY_NORMAL,
        };

        $options                    = $verbosity;

        $this->consoleOutput->writeln($message, $options);
    }
}
