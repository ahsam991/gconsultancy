<?php

namespace App\Console\Commands;

use App\Models\Backup;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class BackupDb extends Command
{
    protected $signature = 'app:backup-db {--type=database : Backup type (database)}';

    protected $description = 'Back up the database (mysqldump --single-transaction for MySQL, SQLite file copy otherwise) into storage/app/backups.';

    public function handle(): int
    {
        $type = (string) $this->option('type');
        if ($type !== 'database') {
            $this->warn('Only database backups are supported by this command; files backups are a manual note.');
        }

        Storage::disk('local')->makeDirectory('backups');
        $stamp = now()->format('Ymd-His');
        $driver = config('database.default');

        if (in_array($driver, ['mysql', 'mariadb'], true)) {
            $mysqldump = trim((string) shell_exec('command -v mysqldump 2>/dev/null'));
            if ($mysqldump !== '') {
                $host = config('database.connections.mysql.host');
                $port = config('database.connections.mysql.port');
                $db = config('database.connections.mysql.database');
                $user = config('database.connections.mysql.username');
                $pass = config('database.connections.mysql.password');
                $filename = "db-backup-{$stamp}.sql";
                $fullPath = Storage::disk('local')->path('backups/'.$filename);
                $cmd = sprintf(
                    '%s --single-transaction -h %s -P %s -u %s %s %s > %s 2>&1',
                    escapeshellcmd($mysqldump),
                    escapeshellarg((string) $host),
                    escapeshellarg((string) $port),
                    escapeshellarg((string) $user),
                    $pass !== '' && $pass !== null ? '-p'.escapeshellarg((string) $pass) : '',
                    escapeshellarg((string) $db),
                    escapeshellarg($fullPath)
                );
                exec($cmd, $out, $code);
                $sizeKb = is_file($fullPath) ? (int) ceil(filesize($fullPath) / 1024) : 0;
                Backup::create([
                    'type' => 'database',
                    'path' => 'backups/'.$filename,
                    'size_kb' => $sizeKb,
                    'status' => $code === 0 ? 'completed' : 'failed',
                    'created_by' => null,
                    'notes' => 'app:backup-db mysqldump --single-transaction',
                ]);
                $this->info($code === 0 ? "Database backup stored: backups/{$filename}" : 'mysqldump failed.');

                return $code === 0 ? 0 : 1;
            }
            $this->warn('mysqldump not available; falling back to file copy attempt.');
        }

        $source = config('database.connections.sqlite.database', database_path('database.sqlite'));
        $filename = "db-backup-{$stamp}.sqlite";
        if (! is_file((string) $source)) {
            $this->error('Database file not found: '.$source);
            Backup::create([
                'type' => 'database',
                'path' => 'backups/'.$filename,
                'size_kb' => 0,
                'status' => 'failed',
                'created_by' => null,
                'notes' => 'app:backup-db source missing',
            ]);

            return 1;
        }

        Storage::disk('local')->put('backups/'.$filename, file_get_contents((string) $source));
        $sizeKb = (int) ceil(Storage::disk('local')->size('backups/'.$filename) / 1024);
        Backup::create([
            'type' => 'database',
            'path' => 'backups/'.$filename,
            'size_kb' => $sizeKb,
            'status' => 'completed',
            'created_by' => null,
            'notes' => 'app:backup-db SQLite copy',
        ]);
        $this->info("Database backup stored: backups/{$filename}");

        return 0;
    }
}
