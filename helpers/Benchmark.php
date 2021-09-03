<?php

namespace app\helpers;

/**
 * https://stackoverflow.com/questions/19973037/benchmark-memory-usage-in-php
 * Class Benchmark
 */
class Benchmark
{
    private static int $max = 0;
    private static int $memory = 0;

    public static function memoryTick(): void
    {
        self::$memory = memory_get_usage() - self::$memory;
        self::$max = self::$memory > self::$max ? self::$memory : self::$max;
        self::$memory = memory_get_usage();
    }

    public static function benchmarkWithCallback(callable $function, $args = null): array
    {
        declare(ticks=1);
        self::$memory = memory_get_usage();
        self::$max = 0;

        register_tick_function('call_user_func_array', [__CLASS__, 'memoryTick'], []);
        $start = microtime(true);
        is_array($args) ? call_user_func_array($function, $args) : $function();
        $time = microtime(true) - $start;
        unregister_tick_function('call_user_func_array');

        return [
            'memory' => self::$max,
            'time' => round($time, 4),
        ];
    }
}