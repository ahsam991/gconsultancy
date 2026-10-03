<?php

namespace App\Http\Controllers;

use App\Models\Backup;
use App\Models\MailLog;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class SystemController extends Controller
{
    protected function ensureAdmin(): void
    {
        if (auth()->user()?->role?->name !== 'admin') {
            abort(403, 'Admin access only.');
        }
    }

    public function health()
    {
        $this->ensureAdmin();

        try {
            DB::connection()->getPdo();
            $dbStatus = 'connected ('.config('database.default').')';
            $dbOk = true;
        } catch (\Throwable $e) {
            $dbStatus = 'failed: '.$e->getMessage();
            $dbOk = false;
        }

        $storagePath = storage_path();
        $diskTotal = @disk_total_space($storagePath) ?: 0;
        $diskFree = @disk_free_space($storagePath) ?: 0;
        $diskUsed = max(0, $diskTotal - $diskFree);

        $queueSize = Schema::hasTable('jobs') ? DB::table('jobs')->count() : 0;
        $failedCount = Schema::hasTable('failed_jobs') ? DB::table('failed_jobs')->count() : 0;
        $lastBackup = Backup::orderByDesc('created_at')->first();

        $recentErrors = 0;
        $logTail = [];
        $logFile = storage_path('logs/laravel.log');
        if (is_readable($logFile)) {
            $lines = @file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [];
            $logTail = array_slice($lines, -50);
            foreach ($logTail as $line) {
                if (str_contains(strtolower($line), 'error')) {
                    $recentErrors++;
                }
            }
        }

        return view('system.health', [
            'phpVersion' => PHP_VERSION,
            'laravelVersion' => app()->version(),
            'dbStatus' => $dbStatus,
            'dbOk' => $dbOk,
            'diskTotal' => $diskTotal,
            'diskFree' => $diskFree,
            'diskUsed' => $diskUsed,
            'queueSize' => $queueSize,
            'failedCount' => $failedCount,
            'lastBackup' => $lastBackup,
            'recentErrors' => $recentErrors,
            'logTail' => array_slice($logTail, -20),
        ]);
    }

    public function backups()
    {
        $this->ensureAdmin();
        $backups = Backup::with('creator')->orderByDesc('created_at')->paginate(15);

        return view('system.backups', compact('backups'));
    }

    public function backupStore(Request $request)
    {
        $this->ensureAdmin();
        $data = $request->validate([
            'type' => 'required|in:database,files',
            'notes' => 'nullable|string|max:1000',
        ]);

        if ($data['type'] === 'files') {
            Backup::create([
                'type' => 'files',
                'path' => 'storage/app (manual copy — see notes)',
                'size_kb' => 0,
                'status' => 'noted',
                'created_by' => auth()->id(),
                'notes' => $data['notes'] ?? 'Files backup: copy storage/app via server snapshot or object storage versioning.',
            ]);
            AuditService::log('backup.noted', null, null, ['type' => 'files']);

            return redirect()->route('system.backups')->with('status', 'Files backup noted. Use server snapshot for storage/app.');
        }

        $result = $this->runDatabaseBackup($data['notes'] ?? null);

        return redirect()->route('system.backups')->with('status', $result);
    }

    protected function runDatabaseBackup(?string $notes): string
    {
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
                    'created_by' => auth()->id(),
                    'notes' => $notes ?? 'mysqldump --single-transaction',
                ]);

                return $code === 0 ? 'Database backup completed.' : 'mysqldump failed — see backups list.';
            }
        }

        // Fallback: SQLite file copy (or MySQL without mysqldump available).
        $source = config('database.connections.sqlite.database', database_path('database.sqlite'));
        $filename = "db-backup-{$stamp}.sqlite";
        $destRelative = 'backups/'.$filename;
        if (is_file((string) $source)) {
            Storage::disk('local')->put($destRelative, file_get_contents((string) $source));
            $sizeKb = (int) ceil(Storage::disk('local')->size($destRelative) / 1024);
            Backup::create([
                'type' => 'database',
                'path' => $destRelative,
                'size_kb' => $sizeKb,
                'status' => 'completed',
                'created_by' => auth()->id(),
                'notes' => $notes ?? 'SQLite file copy',
            ]);

            return 'Database backup completed (SQLite copy).';
        }

        Backup::create([
            'type' => 'database',
            'path' => $destRelative,
            'size_kb' => 0,
            'status' => 'failed',
            'created_by' => auth()->id(),
            'notes' => $notes ?? 'No database file found.',
        ]);

        return 'Backup failed: database file not found.';
    }

    public function backupDownload(Backup $backup)
    {
        // Policy: admin only, enforced twice (route middleware + here).
        $this->ensureAdmin();
        if (! Storage::disk('local')->exists($backup->path)) {
            abort(404, 'Backup file not found on disk.');
        }
        AuditService::log('backup.downloaded', null, null, ['id' => $backup->id]);

        return Storage::disk('local')->download($backup->path);
    }

    public function mailLogs(Request $request)
    {
        $this->ensureAdmin();
        $query = MailLog::orderByDesc('created_at');
        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }
        $logs = $query->paginate(20)->withQueryString();

        return view('system.mail-logs', compact('logs'));
    }

    public function queue(Request $request)
    {
        $this->ensureAdmin();
        $jobs = Schema::hasTable('jobs') ? DB::table('jobs')->orderBy('created_at')->paginate(15, ['*'], 'jobs_page') : collect();
        $failed = Schema::hasTable('failed_jobs')
            ? DB::table('failed_jobs')->orderByDesc('failed_at')->paginate(15, ['*'], 'failed_page')
            : collect();

        return view('system.queue', compact('jobs', 'failed'));
    }

    public function queueRetry(int $failedId)
    {
        $this->ensureAdmin();
        abort_unless(Schema::hasTable('failed_jobs') && Schema::hasTable('jobs'), 404);
        $failed = DB::table('failed_jobs')->where('id', $failedId)->first();
        abort_unless($failed, 404);
        DB::table('jobs')->insert([
            'queue' => $failed->queue ?? 'default',
            'payload' => $failed->payload,
            'attempts' => 0,
            'available_at' => now()->timestamp,
            'created_at' => now()->timestamp,
        ]);
        DB::table('failed_jobs')->where('id', $failedId)->delete();
        AuditService::log('queue.retried', null, null, ['failed_job_id' => $failedId]);

        return redirect()->route('system.queue')->with('status', 'Failed job re-queued.');
    }

    public function queueDelete(Request $request, int $id)
    {
        $this->ensureAdmin();
        $queue = $request->string('queue', 'failed')->toString();
        if ($queue === 'jobs' && Schema::hasTable('jobs')) {
            DB::table('jobs')->where('id', $id)->delete();
        } else {
            abort_unless(Schema::hasTable('failed_jobs'), 404);
            DB::table('failed_jobs')->where('id', $id)->delete();
        }
        AuditService::log('queue.deleted', null, null, ['queue' => $queue, 'id' => $id]);

        return redirect()->route('system.queue')->with('status', 'Job deleted.');
    }
}
