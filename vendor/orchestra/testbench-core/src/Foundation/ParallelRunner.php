<?php

namespace Orchestra\Testbench\Foundation;

use function Orchestra\Testbench\container;

class ParallelRunner extends \Illuminate\Testing\ParallelRunner
{
    /**
     * Creates the application.
     *
     * @return \Illuminate\Contracts\Foundation\Application
     */
    protected function createApplication()
    {
        return container()->createApplication();
    }
}
