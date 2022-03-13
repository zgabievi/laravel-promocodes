<?php

namespace Zorb\Promocodes\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Zorb\Promocodes\Facades\Promocodes;

class Create extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'promocodes:create
                            {--mask= : Mask for generating codes}
                            {--characters= : Characters to use to generate codes from}
                            {--count=1 : Number of codes to be generated}
                            {--unlimited : Whether usages should be unlimited}
                            {--usages=1 : How many times code can be used}
                            {--multi-use : Whether code can be used multiple times from same user}
                            {--user= : The ID of the user who should be bound to promocodes}
                            {--bound-to-user : Whether codes should be bound to users}
                            {--expiration= : Datetime string for promocodes expiration}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create promocodes in database';

    /**
     * @return int
     */
    public function handle(): int
    {
        $count = (int)$this->option('count');
        $mask = $this->option('mask');
        $characters = $this->option('characters');
        $unlimited = $this->option('unlimited');
        $usages = $this->option('usages');
        $multiUse = $this->option('multi-use');
        $userId = $this->option('user');
        $boundToUser = $this->option('bound-to-user');
        $expiration = $this->option('expiration');

        $promocodes = Promocodes::count($count);

        if ($mask) {
            $promocodes = $promocodes->mask($mask);
        }

        if (isset($characters)) {
            $promocodes = $promocodes->characters($characters);
        }

        if ($unlimited) {
            $promocodes = $promocodes->unlimited();
        }

        if (isset($usages)) {
            $promocodes = $promocodes->usages((int)$usages);
        }

        if ($multiUse) {
            $promocodes = $promocodes->multiUse();
        }

        if ($userId) {
            $user = app(config('promocodes.models.users.model'))->find($userId);

            if (!$user) {
                $this->error("🥺️ User with ID `{$userId}` doesn't exist!");
                return 1;
            }

            $promocodes = $promocodes->user($user);
        }

        if ($boundToUser) {
            $promocodes = $promocodes->boundToUser();
        }

        if ($expiration) {
            $dateTime = Carbon::parse($expiration);
            $promocodes = $promocodes->expiration($dateTime);
        }

        $codes = $promocodes->create();

        $this->info("🎉 Created promocodes with codes: {$codes->pluck('code')->implode(', ')}");

        return 0;
    }
}
