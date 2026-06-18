<?php
use App\Jobs\DetectMissingDailyReportsJob;use Illuminate\Support\Facades\Artisan;use Illuminate\Support\Facades\Schedule;
Artisan::command('workproof:detect-missing-reports', function () {DetectMissingDailyReportsJob::dispatchSync();$this->info('Missing daily report detection completed.');})->purpose('Detect missing tenant daily reports after workspace deadlines.');
Schedule::command('workproof:detect-missing-reports')->hourly();
