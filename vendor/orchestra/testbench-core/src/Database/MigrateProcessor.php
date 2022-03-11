<?php

namespace Orchestra\Testbench\Database;

use Illuminate\Database\Migrations\Migrator;
use function Orchestra\Testbench\artisan;
use Orchestra\Testbench\Contracts\TestCase;

class MigrateProcessor
{
    /**
     * The testbench instance.
     *
     * @var \Orchestra\Testbench\Contracts\TestCase
     */
    protected $testbench;

    /**
     * The migrator options.
     *
     * @var array
     */
    protected $options = [];

    /**
     * Construct a new schema migrator.
     *
     * @param \Orchestra\Testbench\Contracts\TestCase  $testbench
     * @param array  $options
     */
    public function __construct(TestCase $testbench, array $options = [])
    {
        $this->testbench = $testbench;
        $this->options = $options;
    }

    /**
     * Run migration.
     *
     * @return $this
     */
    public function up()
    {
        $this->dispatch('migrate');

        return $this;
    }

    /**
     * Rollback migration.
     *
     * @return $this
     */
    public function rollback()
    {
        $this->dispatch('migrate:rollback');

        return $this;
    }

    /**
     * Dispatch artisan command.
     *
     * @param  string $command
     *
     * @return void
     */
    protected function dispatch(string $command): void
    {
        artisan($this->testbench, $command, $this->options);
    }
}
