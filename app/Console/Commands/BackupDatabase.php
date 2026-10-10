<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class BackupDatabase extends Command
{
    protected $signature = 'db:backup {--disk=local : Storage disk for backup}';
    protected $description = 'Backup database to storage';

    public function handle(): int
    {
        $filename = 'backup-' . now()->format('Y-m-d_H-i-s') . '.sql';
        $path = storage_path('app/backups');

        if (!is_dir($path)) {
            mkdir($path, 0755, true);
        }

        $host = config('database.connections.mysql.host');
        $port = config('database.connections.mysql.port');
        $database = config('database.connections.mysql.database');
        $username = config('database.connections.mysql.username');
        $password = config('database.connections.mysql.password');

        $command = sprintf(
            'mysqldump --host=%s --port=%s --user=%s --password=%s %s > %s',
            escapeshellarg($host),
            escapeshellarg($port),
            escapeshellarg($username),
            escapeshellarg($password),
            escapeshellarg($database),
            escapeshellarg($path . DIRECTORY_SEPARATOR . $filename)
        );

        exec($command . ' 2>&1', $output, $exitCode);

        if ($exitCode === 0) {
            $this->info("Backup created: {$filename}");

            // Keep only last 7 backups
            $files = glob($path . DIRECTORY_SEPARATOR . 'backup-*.sql');
            if (count($files) > 7) {
                usort($files, fn($a, $b) => filemtime($a) <=> filemtime($b));
                foreach (array_slice($files, 0, count($files) - 7) as $oldFile) {
                    unlink($oldFile);
                }
            }
        } else {
            $this->error('Backup failed: ' . implode("\n", $output));
        }

        return $exitCode;
    }
}
