<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Symfony\Component\Process\Process;

class DatabaseBackupCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:backup {--path= : Custom path to store the backup (defaults to storage/backups)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a MySQL database backup using mysqldump and store it under storage/backups';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $database = config('database.connections.mysql');

        $backupDir = $this->option('path') ?: storage_path('backups');
        if (!File::exists($backupDir)) {
            File::makeDirectory($backupDir, 0755, true);
        }

        $filename = 'db-backup-' . now()->format('Ymd_His') . '.sql';
        $fullPath = rtrim($backupDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $filename;

        $command = [
            'mysqldump',
            '-h',
            $database['host'],
            '-P',
            $database['port'],
            '-u',
            $database['username'],
            $database['database'],
        ];

        $process = new Process($command);
        // Avoid exposing password in arguments
        $process->setEnv(['MYSQL_PWD' => $database['password']]);
        $process->run();

        if (! $process->isSuccessful()) {
            $this->error('Backup failed: ' . $process->getErrorOutput());
            return Command::FAILURE;
        }

        File::put($fullPath, $process->getOutput());
        $this->info('Backup created: ' . $fullPath);

        return Command::SUCCESS;
    }
}
