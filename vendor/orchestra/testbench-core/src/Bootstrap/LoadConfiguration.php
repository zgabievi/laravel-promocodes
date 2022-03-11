<?php

namespace Orchestra\Testbench\Bootstrap;

use Generator;
use Illuminate\Config\Repository;
use Illuminate\Contracts\Config\Repository as RepositoryContract;
use Illuminate\Contracts\Foundation\Application;
use Symfony\Component\Finder\Finder;

final class LoadConfiguration
{
    /**
     * Bootstrap the given application.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     *
     * @return void
     */
    public function bootstrap(Application $app): void
    {
        $app->instance('config', $config = new Repository([]));

        $this->loadConfigurationFiles($app, $config);

        mb_internal_encoding('UTF-8');
    }

    /**
     * Load the configuration items from all of the files.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     * @param  \Illuminate\Contracts\Config\Repository  $config
     *
     * @return void
     */
    private function loadConfigurationFiles(Application $app, RepositoryContract $config): void
    {
        foreach ($this->getConfigurationFiles($app) as $key => $path) {
            $config->set($key, require $path);
        }
    }

    /**
     * Get all of the configuration files for the application.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     *
     * @return \Generator
     */
    private function getConfigurationFiles(Application $app): Generator
    {
        if (! is_dir($path = $app->basePath('config'))) {
            $path = realpath(__DIR__.'/../../laravel/config');
        }

        if (\is_string($path)) {
            foreach (Finder::create()->files()->name('*.php')->in($path) as $file) {
                yield basename($file->getRealPath(), '.php') => $file->getRealPath();
            }
        }
    }
}
