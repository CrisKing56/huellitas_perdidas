<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\DatabaseBackupService;
use Carbon\Carbon;

class AdminBackupController extends Controller
{
    public function __construct(
        protected DatabaseBackupService $backupService
    ) {
    }

    public function index()
    {
        $backups = collect($this->backupService->listBackups())
            ->map(function ($backup) {
                $backup['size_human'] = $this->formatBytes((int) $backup['size']);
                $backup['last_modified_human'] = Carbon::createFromTimestamp($backup['last_modified'])
                    ->locale('es')
                    ->translatedFormat('d \\d\\e F \\d\\e Y, H:i');

                return $backup;
            });

        return view('admin.backups.index', compact('backups'));
    }

    public function store()
    {
        try {
            $filename = $this->backupService->createBackup();

            return redirect()
                ->route('admin.backups.index')
                ->with('success', "Respaldo generado correctamente: {$filename}");
        } catch (\Throwable $e) {
            report($e);

            return redirect()
                ->route('admin.backups.index')
                ->with('error', 'No se pudo generar el respaldo. Revisa el log del sistema.');
        }
    }

    public function download(string $file)
    {
        $file = basename($file);

        if (!$this->backupService->exists($file)) {
            abort(404);
        }

        $path = storage_path('app/' . $this->backupService->downloadPath($file));

        if (!file_exists($path)) {
            abort(404);
        }

        return response()->download($path, $file, [
            'Content-Type' => 'application/sql',
        ]);
    }

    public function destroy(string $file)
    {
        $file = basename($file);

        if (!$this->backupService->exists($file)) {
            return redirect()
                ->route('admin.backups.index')
                ->with('error', 'El archivo de respaldo no existe.');
        }

        $this->backupService->delete($file);

        return redirect()
            ->route('admin.backups.index')
            ->with('success', 'Respaldo eliminado correctamente.');
    }

    protected function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min((int) $pow, count($units) - 1);

        $bytes /= (1 << (10 * $pow));

        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}