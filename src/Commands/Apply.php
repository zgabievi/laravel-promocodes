<?php

namespace Zorb\Promocodes\Commands;

use Zorb\Promocodes\Facades\Promocodes;
use Illuminate\Console\Command;

class Apply extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'promocodes:apply
                            {code : The code which should be applied to}
                            {--currency= : The ID of the currency}
                            {--user= : The ID of the user, who should apply to promocode}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Apply user or guest to a specific promocode';

    /**
     * @return int
     */
    public function handle(): int
    {
        $code = $this->argument('code');
        $currencyId = $this->option('currency');
        $userId = $this->option('user');

        $currency = app(config('promocodes.models.currency.model'))->find($currencyId);

        if (!$currency) {
            $this->error("🥺️ Currency with ID `{$currencyId}` doesn't exist!");
            return 1;
        }

        $promocodes = Promocodes::code($code)->currency($currency);

        if ($userId) {
            $user = app(config('promocodes.models.users.model'))->find($userId);

            if (!$user) {
                $this->error("🥺️ User with ID `{$userId}` doesn't exist!");
                return 1;
            }

            $promocodes = $promocodes->user($user);
        }

        if ($promocodes->apply()) {
            if ($userId) {
                $this->info("🎉 Promocode `{$code}` applied to user with id `{$userId}`");
            } else {
                $this->info("🎉 Promocode `{$code}` applied to guest");
            }
        }

        return 0;
    }
}
