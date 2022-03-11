<?php

namespace Spatie\LaravelRay\Watchers;

use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\Facades\DB;
use Spatie\LaravelRay\Payloads\ExecutedQueryPayload;
use Spatie\LaravelRay\Ray;
use Spatie\Ray\Settings\Settings;

class SlowQueryWatcher extends QueryWatcher
{
    protected $minimumTimeInMs = 0;

    public function register(): void
    {
        $settings = app(Settings::class);

        $this->enabled = $settings->send_slow_queries_to_ray ?? false;

        DB::listen(function (QueryExecuted $query) {
            if (! $this->enabled()) {
                return;
            }

            $ray = app(Ray::class);

            if ($query->time >= $this->minimumTimeInMs) {
                $payload = new ExecutedQueryPayload($query);

                $ray->sendRequest($payload);
            }

            optional($this->rayProxy)->applyCalledMethods($ray);
        });
    }

    public function setMinimumTimeInMilliseconds(float $milliseconds): self
    {
        $this->minimumTimeInMs = $milliseconds;

        return $this;
    }
}
