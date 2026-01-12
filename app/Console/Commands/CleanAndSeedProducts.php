<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CleanAndSeedProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:clean-seed';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean categories and products tables, then seed from SISTEM.xlsx';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (!$this->confirm('This will delete all categories and products. Do you want to continue?')) {
            $this->info('Operation cancelled.');
            return 0;
        }

        $this->info('Starting clean and seed process...');

        // Seed categories
        $this->info('Seeding categories from SISTEM.xlsx...');
        $this->call('db:seed', ['--class' => 'CategorySeeder']);

        // Seed products
        $this->info('Seeding products from SISTEM.xlsx...');
        $this->call('db:seed', ['--class' => 'ProductSeeder']);

        $this->info('✓ Clean and seed completed successfully!');

        return 0;
    }
}
