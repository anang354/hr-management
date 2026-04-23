<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class DeactivateExpiredContracts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:deactivate-expired-contracts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
        $affected = \App\Models\Contract::query()
            ->where('is_active', true)
            ->where('end_date', '<', now()->toDateString())
            ->update(['is_active' => false]);

        $this->info("Deactivated {$affected} contracts");
    }
}
