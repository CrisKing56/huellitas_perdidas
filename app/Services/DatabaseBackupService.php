<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class DatabaseBackupService
{
    protected string $backupDirectory = 'backups/database';

    public function createBackup(): string
    {
        $connectionName = config('database.default');
        $connection = DB::connection($connectionName);

        if ($connection->getDriverName() !== 'mysql') {
            throw new RuntimeException('El servicio de respaldo actual solo soporta MySQL.');
        }

        $pdo = $connection->getPdo();
        $databaseName = $connection->getDatabaseName();

        $tables = [];
        $tablesStatement = $pdo->query('SHOW TABLES');

        while ($row = $tablesStatement->fetch(\PDO::FETCH_NUM)) {
            $tables[] = $row[0];
        }

        if (empty($tables)) {
            throw new RuntimeException('No se encontraron tablas para respaldar.');
        }

        $sql = [];
        $sql[] = "-- =========================================\n";
        $sql[] = "-- Huellitas Perdidas - Respaldo de BD\n";
        $sql[] = "-- Base de datos: {$databaseName}\n";
        $sql[] = "-- Fecha: " . now()->format('Y-m-d H:i:s') . "\n";
        $sql[] = "-- =========================================\n\n";
        $sql[] = "SET FOREIGN_KEY_CHECKS=0;\n";
        $sql[] = "SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";\n";
        $sql[] = "START TRANSACTION;\n\n";

        foreach ($tables as $table) {
            $safeTable = str_replace('`', '``', $table);

            $createStatement = $pdo->query("SHOW CREATE TABLE `{$safeTable}`");
            $createRow = $createStatement->fetch(\PDO::FETCH_ASSOC);

            if (!$createRow) {
                continue;
            }

            $createSql = array_values($createRow)[1] ?? null;

            $sql[] = "-- -----------------------------------------\n";
            $sql[] = "-- Estructura de tabla `{$table}`\n";
            $sql[] = "-- -----------------------------------------\n";
            $sql[] = "DROP TABLE IF EXISTS `{$safeTable}`;\n";
            $sql[] = $createSql . ";\n\n";

            $dataStatement = $pdo->query("SELECT * FROM `{$safeTable}`");

            while ($row = $dataStatement->fetch(\PDO::FETCH_ASSOC)) {
                $columns = array_map(
                    fn ($column) => '`' . str_replace('`', '``', $column) . '`',
                    array_keys($row)
                );

                $values = array_map(function ($value) use ($pdo) {
                    if ($value === null) {
                        return 'NULL';
                    }

                    if (is_bool($value)) {
                        return $value ? '1' : '0';
                    }

                    return $pdo->quote((string) $value);
                }, array_values($row));

                $sql[] = "INSERT INTO `{$safeTable}` (" . implode(', ', $columns) . ") VALUES (" . implode(', ', $values) . ");\n";
            }

            $sql[] = "\n";
        }

        $sql[] = "COMMIT;\n";
        $sql[] = "SET FOREIGN_KEY_CHECKS=1;\n";

        $filename = 'huellitas_backup_' . now()->format('Y_m_d_His') . '.sql';

        Storage::disk('local')->makeDirectory($this->backupDirectory);
        Storage::disk('local')->put($this->backupDirectory . '/' . $filename, implode('', $sql));

        $this->pruneOldBackups(15);

        return $filename;
    }

    public function listBackups(): array
    {
        $files = collect(Storage::disk('local')->files($this->backupDirectory))
            ->filter(fn ($file) => str_ends_with($file, '.sql'))
            ->map(function ($file) {
                return [
                    'path' => $file,
                    'name' => basename($file),
                    'size' => Storage::disk('local')->size($file),
                    'last_modified' => Storage::disk('local')->lastModified($file),
                ];
            })
            ->sortByDesc('last_modified')
            ->values()
            ->all();

        return $files;
    }

    public function exists(string $filename): bool
    {
        return Storage::disk('local')->exists($this->backupDirectory . '/' . basename($filename));
    }

    public function downloadPath(string $filename): string
    {
        return $this->backupDirectory . '/' . basename($filename);
    }

    public function delete(string $filename): void
    {
        $path = $this->downloadPath($filename);

        if (Storage::disk('local')->exists($path)) {
            Storage::disk('local')->delete($path);
        }
    }

    protected function pruneOldBackups(int $keep = 15): void
    {
        $files = collect($this->listBackups());

        if ($files->count() <= $keep) {
            return;
        }

        $files->slice($keep)->each(function ($file) {
            Storage::disk('local')->delete($file['path']);
        });
    }
}