<?php
namespace App\Console\Commands;use App\Jobs\ArchiveOldActivityLogsJob;use Illuminate\Console\Command;class ArchiveOldLogsCommand extends Command{protected $signature='workproof:archive-old-logs';protected $description='Archive old activity logs';public function handle():int{ArchiveOldActivityLogsJob::dispatch();$this->info('Log archive queued.');return self::SUCCESS;}}
