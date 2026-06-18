<?php
namespace App\Console\Commands;use App\Jobs\DetectMissingDailyReportsJob;use Illuminate\Console\Command;
class DetectMissingDailyReportsCommand extends Command{protected $signature='workproof:detect-missing-reports';protected $description='Detect missing tenant daily reports after workspace deadlines.';public function handle():int{DetectMissingDailyReportsJob::dispatchSync();$this->info('Missing daily report detection completed.');return self::SUCCESS;}}
