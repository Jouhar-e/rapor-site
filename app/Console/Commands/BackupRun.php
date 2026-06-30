<?php

namespace App\Console\Commands;

use App\Services\BackupService;
use Illuminate\Console\Command;

class BackupRun extends Command
{
    protected $signature = 'backup:run {--type=database : Type of backup (database)}';

    protected $description = 'Run database backup';

    public function handle(BackupService $backupService): int
    {
        $type = $this->option('type');

        $this->info("Starting {$type} backup...");

        try {
            $filename = $backupService->backupDatabase();
            $this->info("Backup completed: {$filename}");

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error("Backup failed: {$e->getMessage()}");

            return Command::FAILURE;
        }
    }
}
