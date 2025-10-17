<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Company;
use App\Jobs\RunBulkDepreciation;

class CalculateDepreciation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:calculate-depreciation';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Calculate and record monthly asset depreciation';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        
        $this->info('Dispatching monthly depreciation jobs for all active companies...');

        // Ambil semua ID perusahaan yang aktif
        $companyIds = Company::pluck('id');

        if ($companyIds->isEmpty()) {
            $this->info('No active companies found.');
            return 0;
        }

        // Kirim satu job untuk setiap perusahaan ke dalam antrian
        foreach ($companyIds as $companyId) {
            RunBulkDepreciation::dispatch($companyId);
            $this->info("Job dispatched for Company ID: {$companyId}");
        }

        $this->info('All depreciation jobs have been dispatched successfully.');
        return 0;
    }
}
