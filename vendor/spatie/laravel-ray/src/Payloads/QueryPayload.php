<?php

namespace Spatie\LaravelRay\Payloads;

use Illuminate\Database\Query\Builder;
use Spatie\Ray\Payloads\Payload;

class QueryPayload extends Payload
{
    /** @var \Illuminate\Database\Query\Builder */
    protected $query;

    public function __construct(Builder $query)
    {
        $this->query = $query;
    }

    public function getType(): string
    {
        return 'executed_query';
    }

    public function getContent(): array
    {
        return [
            'sql' => $this->query->toSql(),
            'bindings' => $this->query->getBindings(),
        ];
    }
}
