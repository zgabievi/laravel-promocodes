<?php

namespace Zorb\Promocodes\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class PromocodeUser extends Pivot
{
    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = true;

    /**
     * Create a new Eloquent model instance.
     *
     * @param array $attributes
     * @return void
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->setTable(config('promocodes.models.pivot.table_name'));
    }
}
