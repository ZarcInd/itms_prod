<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Jobs\ProcessRawDataExport;

class ProcessPendingExports extends Command
{
    protected $signature = 'exports:process-pending {--date= : Process exports for specific date (Y-m-d)}';
    protected $description = 'Process pending raw data exports';

    public function handle()
    {
        $date = $this->option('date');
        
        $query = DB::table('rawdatalist')->where('status', 'incompleted');
        
        if ($date) {
            $formattedDate = \Carbon\Carbon::parse($date)->format('d-m-Y');
            $query->where('date_filter', $formattedDate);
            $this->info("Processing exports for date: {$formattedDate}");
        } else {
            $this->info("Processing all pending exports");
        }

        $pendingExports = $query->orderBy('created_at', 'asc')->get();

        if ($pendingExports->isEmpty()) {
            $this->info('No pending exports found.');
            return;
        }

        $this->info("Found {$pendingExports->count()} pending exports");

        foreach ($pendingExports as $export) {
            ProcessRawDataExport::dispatch($export->id);
            $this->info("Queued export ID: {$export->id}");
        }

        $this->info('All pending exports have been queued for processing.');
    }
}

