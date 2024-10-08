<?php
declare(strict_types=1);

namespace IfCastle\Application;

/**
 * Asynchronous timer scheduling interface.
 *
 * @package IfCastle\Application
 */
interface ScheduleTimerInterface
{
    /**
     * Schedules a callback to execute in the next iteration of the event loop.
     *
     * @param   callable            $callback
     * @return  void
     */
    public function defer(callable $callback): void;
    
    /**
     * Schedules a callback to execute after a specified delay.
     *
     * @param   float|int           $delay
     * @param   callable            $callback
     *
     * @return  int|string
     */
    public function delay(float|int $delay, callable $callback): int|string;
    
    /**
     * Schedules a callback to execute periodically.
     *
     * $callback will be called repeatedly, with $interval seconds between each call.
     * $callback can implement a FreeInterface. So when a process is terminated, $callback->free() should be called.
     *
     * @param   float|int           $interval  Interval in seconds
     * @param   callable            $callback
     *
     * @return  int|string
     */
    public function interval(float|int $interval, callable $callback): int|string;
    
    /**
     * Cancels a callback scheduled with interval().
     *
     * @param   int|string          $timerId
     *
     * @return  void
     */
    public function clear(int|string $timerId): void;
}