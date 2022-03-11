<?php

namespace Orchestra\Testbench\Foundation\Console;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use NunoMaduro\Collision\Adapters\Laravel\Commands\TestCommand as Command;

class TestCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'package:test
        {--without-tty : Disable output to TTY}
        {--coverage : Indicates whether the coverage information should be collected}
        {--min= : Indicates the minimum threshold enforcement for coverage}
        {--parallel : Indicates if the tests should run in parallel}
        {--recreate-databases : Indicates if the test databases should be re-created}
    ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run the package tests';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        if (! \defined('TESTBENCH_WORKING_PATH')) {
            $this->setHidden(true);
        }
    }

    /**
     * Get the array of arguments for running PHPUnit.
     *
     * @param array $options
     *
     * @return array
     */
    protected function phpunitArguments($options)
    {
        $options = Collection::make($options)
            ->merge(['--printer=NunoMaduro\\Collision\\Adapters\\Phpunit\\Printer'])
            ->reject(static function ($option) {
                return Str::startsWith($option, '--env=')
                    || $option == '--coverage'
                    || Str::startsWith($option, '--min');
            })->values()->all();

        return array_merge($this->commonArguments(), ['--configuration=./'], $options);
    }

    /**
     * Get the array of arguments for running Paratest.
     *
     * @param array $options
     *
     * @return array
     */
    protected function paratestArguments($options)
    {
        $options = Collection::make($options)
            ->reject(static function ($option) {
                return Str::startsWith($option, '--env=')
                    || $option == '--coverage'
                    || Str::startsWith($option, '--min')
                    || Str::startsWith($option, '-p')
                    || Str::startsWith($option, '--parallel')
                    || Str::startsWith($option, '--recreate-databases');
            })->values()->all();

        return array_merge([
            '--configuration=./',
            "--runner=\Orchestra\Testbench\Foundation\ParallelRunner",
        ], $options);
    }
}
