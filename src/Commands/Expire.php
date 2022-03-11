<?php

namespace Zorb\Promocodes\Commands;

use Illuminate\Console\Command;

class Expire extends Command
{
    //
    protected $signature = 'promocodes:expire';

    //
    protected $description = '';

    //
    public function handle(): int
    {
        return 0;
    }
}
