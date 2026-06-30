<?php

namespace App\Services;

use App\Models\BackupHistory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class BackupService
{
    protected string $disk;

    public function __construct(
        protected BackupHistory $backupHistory,
    ) {
        $this->disk = config('backup.disk', 'local');
    }

    public function backupDatabase(): string
    {
        $filename = 'backup-db-'.now()->format('Y-m-d-H-i-s').'.sql';
        $filePath = "backups/{$filename}";

        $this->backupHistory->create([
            'filename' => $filename,
            'type' => 'database',
            'status' => 'running',
            'started_at' => now(),
        ]);

        $database = config('database.connections.mysql.database');
        $username = config('database.connections.mysql.username');
        $password = config('database.connections.mysql.password');
        $host = config('database.connections.mysql.host');
        $port = config('database.connections.mysql.port', 3306);

        $sqlContent = "-- Database Backup: {$database}\n";
        $sqlContent .= '-- Generated: '.now()."\n\n";

        $tables = DB::select('SHOW TABLES');
        $tableKey = "Tables_in_{$database}";

        foreach ($tables as $table) {
            $tableName = $table->$tableKey;

            $createTable = DB::select("SHOW CREATE TABLE `{$tableName}`");
            $sqlContent .= "\n-- Table structure for `{$tableName}`\n";
            $sqlContent .= $createTable[0]->{'Create Table'}.";\n\n";

            $rows = DB::table($tableName)->get();

            foreach ($rows as $row) {
                $columns = array_keys((array) $row);
                $values = array_map(fn ($value) => is_null($value) ? 'NULL' : "'".addslashes($value)."'", array_values((array) $row));
                $sqlContent .= "INSERT INTO `{$tableName}` (`".implode('`, `', $columns).'`) VALUES ('.implode(', ', $values).");\n";
            }
        }

        Storage::disk($this->disk)->put($filePath, $sqlContent);

        $this->backupHistory
            ->where('filename', $filename)
            ->where('type', 'database')
            ->update([
                'file_size' => strlen($sqlContent),
                'status' => 'completed',
                'completed_at' => now(),
            ]);

        return Storage::disk($this->disk)->path($filePath);
    }

    public function backupUploads(): string
    {
        $filename = 'backup-uploads-'.now()->format('Y-m-d-H-i-s').'.zip';
        $filePath = "backups/{$filename}";

        $this->backupHistory->create([
            'filename' => $filename,
            'type' => 'uploads',
            'status' => 'running',
            'started_at' => now(),
        ]);

        $uploadDisk = config('filesystems.default');
        $files = Storage::disk($uploadDisk)->allFiles();

        $zip = new \ZipArchive;
        $zipPath = Storage::disk($this->disk)->path($filePath);

        $zipDir = dirname($zipPath);
        if (! is_dir($zipDir)) {
            mkdir($zipDir, 0755, true);
        }

        if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            $this->backupHistory
                ->where('filename', $filename)
                ->where('type', 'uploads')
                ->update([
                    'status' => 'failed',
                    'completed_at' => now(),
                    'notes' => 'Failed to create zip archive.',
                ]);

            throw new \RuntimeException('Failed to create backup archive.');
        }

        foreach ($files as $file) {
            $content = Storage::disk($uploadDisk)->get($file);
            if ($content !== null) {
                $zip->addFromString($file, $content);
            }
        }

        $zip->close();

        $this->backupHistory
            ->where('filename', $filename)
            ->where('type', 'uploads')
            ->update([
                'file_size' => filesize($zipPath),
                'status' => 'completed',
                'completed_at' => now(),
            ]);

        return $zipPath;
    }

    public function restore(string $filePath): bool
    {
        if (! file_exists($filePath)) {
            return false;
        }

        $extension = pathinfo($filePath, PATHINFO_EXTENSION);

        if ($extension === 'sql') {
            $sql = file_get_contents($filePath);
            DB::connection()->getPdo()->exec($sql);

            return true;
        }

        return false;
    }
}
