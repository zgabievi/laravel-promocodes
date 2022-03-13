<?php

namespace Zorb\Promocodes\Commands;

use Illuminate\Console\Command;
use Zorb\Promocodes\Contracts\PromocodeContract;

class Expire extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'promocodes:expire
                            {code : The code which should be expired}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mark promocode as expired';

    /**
     * @return int
     */
    public function handle(): int
    {
        $code = $this->argument('code');

        $promocode = app(PromocodeContract::class)->findByCode($code)->first();

        if (!$promocode) {
            $this->error("🥺️ Promocode `{$code}` doesn't exist!");
            return 1;
        }

        $promocode->update(['expired_at' => now()]);

        $this->info("🎉 Promocode `{$code}` marked as expired");

        return 0;
    }
}
