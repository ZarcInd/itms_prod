<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Console\Commands\ProcessPendingExports;
use Illuminate\Support\Facades\DB;



Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('exports:process-pending {--date= : Process exports for specific date (Y-m-d)}', function () {
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
        \App\Jobs\ProcessRawDataExport::dispatch($export->id);
        $this->info("Queued export ID: {$export->id}");
    }

    $this->info('All pending exports have been queued for processing.');
})->purpose('Process pending raw data exports');

