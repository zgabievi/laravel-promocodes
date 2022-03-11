<?php

namespace Orchestra\Testbench;

use Illuminate\Testing\PendingCommand;
use Orchestra\Testbench\Foundation\Application;

/**
 * Create Laravel application instance.
 *
 * @param  string|null  $basePath
 * @param  callable(\Illuminate\Foundation\Application):void|null  $resolvingCallback
 * @param  array  $options
 *
 * @return \Orchestra\Testbench\Foundation\Application
 */
function container(?string $basePath = null, ?callable $resolvingCallback = null, array $options = [])
{
    return tap(new Application($basePath, $resolvingCallback))->configure($options);
}

/**
 * Run artisan command.
 *
 * @param  \Orchestra\Testbench\Contracts\TestCase  $testbench
 * @param  string  $command
 * @param  array<string, mixed>  $parameters
 *
 * @return \Illuminate\Testing\PendingCommand|int
 */
function artisan(Contracts\TestCase $testbench, string $command, array $parameters = [])
{
    return tap($testbench->artisan($command, $parameters), function ($artisan) {
        if ($artisan instanceof PendingCommand) {
            $artisan->run();
        }
    });
}
