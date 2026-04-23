<?php

namespace App\Console\Commands;

use App\Services\DatabaseBackupService;
use Illuminate\Console\Command;

class BackupDatabaseCommand extends Command
{
    protected $signature = 'database:backup';
    protected $description = 'Genera un respaldo SQL de la base de datos';

    public function handle(DatabaseBackupService $backupService): int
    {
        try {
            $filename = $backupService->createBackup();
            $this->info("Respaldo generado correctamente: {$filename}");
            return self::SUCCESS;
        } catch (\Throwable $e) {
            report($e);
            $this->error('No se pudo generar el respaldo: ' . $e->getMessage());
            return self::FAILURE;
        }
    }
}