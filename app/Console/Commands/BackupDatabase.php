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

        if (! is_dir($path)) {
            mkdir($path, 0755, true);
        }

        // SQLite (testing / small deploys): the database IS a file — copy it.
        // This branch also makes backup/restore testable without mysqldump.
        if (config('database.default') === 'sqlite') {
            $source = config('database.connections.sqlite.database');
            $dest = $path . DIRECTORY_SEPARATOR . pathinfo($filename, PATHINFO_FILENAME) . '.sqlite';
            if (! is_file($source) || ! copy($source, $dest)) {
                $this->error('Backup failed: sqlite source missing or unreadable.');
                return 1;
            }
            $this->info("Backup created: {$dest}");
            $this->prune($path, 'backup-*.sqlite');
            return 0;
        }

        $host = config('database.connections.mysql.host');
        $port = config('database.connections.mysql.port');
        $database = config('database.connections.mysql.database');
        $username = config('database.connections.mysql.username');
        $password = config('database.connections.mysql.password');

        // SECURITY: password via MYSQL_PWD env, never argv (argv leaks to
        // process lists and shell history).
        $command = sprintf(
            'mysqldump --host=%s --port=%s --user=%s %s > %s',
            escapeshellarg($host),
            escapeshellarg($port),
            escapeshellarg($username),
            escapeshellarg($database),
            escapeshellarg($path . DIRECTORY_SEPARATOR . $filename)
        );

        $previousPwd = getenv('MYSQL_PWD');
        putenv('MYSQL_PWD=' . $password);
        try {
            exec($command . ' 2>&1', $output, $exitCode);
        } finally {
            if ($previousPwd === false) {
                putenv('MYSQL_PWD');
            } else {
                putenv('MYSQL_PWD=' . $previousPwd);
            }
        }

        if ($exitCode === 0) {
            $this->info("Backup created: {$filename}");
            $this->prune($path, 'backup-*.sql');
        } else {
            $this->error('Backup failed: ' . implode("\n", $output));
        }

        return $exitCode;
    }

    protected function prune(string $path, string $pattern): void
    {
        $files = glob($path . DIRECTORY_SEPARATOR . $pattern);
        if (count($files) > 7) {
            usort($files, fn ($a, $b) => filemtime($a) <=> filemtime($b));
            foreach (array_slice($files, 0, count($files) - 7) as $oldFile) {
                unlink($oldFile);
            }
        }
    }
}
