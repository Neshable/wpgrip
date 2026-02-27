<?php

namespace App\Console\Commands;

use App\Services\ShelleyManager;
use Illuminate\Console\Command;

class ShelleyCleanup extends Command
{
    protected $signature   = 'shelley:cleanup';
    protected $description = 'Kill idle Shelley instances that have not been accessed recently';

    public function handle(ShelleyManager $manager): void
    {
        $this->info('Cleaning up idle Shelley instances...');
        $manager->killIdle();
        $this->info('Done.');
    }
}
