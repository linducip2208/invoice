<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Backup + restore round-trip on a file sqlite database.
 * (MySQL path shells to mysqldump and needs a server; the file-copy
 * restore logic is what this proves.)
 *
 * NOTE: no RefreshDatabase here on purpose — this test owns its file DB
 * and running migrate inside the trait's transaction breaks sqlite.
 */
class BackupRestoreTest extends TestCase
{
    protected string $file;
    protected ?string $latestBackup = null;

    protected function tearDown(): void
    {
        @unlink($this->file ?? '');
        if ($this->latestBackup) {
            @unlink($this->latestBackup);
        }
        parent::tearDown();
    }

    #[Test]
    public function backup_and_restore_round_trip(): void
    {
        // Backups live on real disk (not :memory:): clear stale files from
        // previous runs so the glob below only sees this test's backup.
        foreach (glob(storage_path('app/backups/backup-*.sqlite')) ?: [] as $stale) {
            @unlink($stale);
        }
        $this->file = storage_path('app/e2e-restore-test.sqlite');
        @unlink($this->file);
        touch($this->file);

        Config::set('database.default', 'sqlite');
        Config::set('database.connections.sqlite.database', $this->file);
        DB::purge('sqlite');

        Artisan::call('migrate', ['--force' => true, '--database' => 'sqlite']);

        $user = User::factory()->create();
        $ws = Workspace::factory()->create();
        $user->workspaces()->attach($ws->id, ['role' => 'admin']);
        Client::factory()->create(['workspace_id' => $ws->id, 'name' => 'Restore Me']);

        $this->assertEquals(0, Artisan::call('db:backup'));
        $backups = glob(storage_path('app/backups/backup-*.sqlite'));
        $this->assertNotEmpty($backups, 'no backup file created');
        $latest = end($backups);
        $this->latestBackup = $latest;

        // Simulate data loss, then restore from the backup file.
        Client::query()->delete();
        $this->assertEquals(0, Client::count());
        copy($latest, $this->file);

        // Reconnect so the restored file is read.
        DB::purge('sqlite');
        $this->assertEquals(1, Client::count());
        $this->assertEquals('Restore Me', Client::first()->name);
    }
}
