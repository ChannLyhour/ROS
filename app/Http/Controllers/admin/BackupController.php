<?php

/**
 * Modified At: 2026-05-01T08:33:14Z
 */

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Spatie\Backup\BackupDestination\BackupDestinationFactory;
use Spatie\Backup\Config\Config;
use Exception;

class BackupController extends Controller
{
    /**
     * Display a listing of backups.
     */
    public function index()
    {
        Gate::authorize('manage-backups');
        $backups = [];
        try {
            $config = Config::fromArray(config('backup'));
            $destinations = BackupDestinationFactory::createFromArray($config);

            foreach ($destinations as $destination) {
                foreach ($destination->backups() as $backup) {
                    $path = $backup->path();
                    $filename = basename($path);
                    
                    $backups[] = [
                        'file_name' => $filename,
                        'file_size' => $this->formatBytes($backup->sizeInBytes()),
                        'last_modified' => $backup->date()->format('Y-m-d H:i:s'),
                        'disk' => $destination->diskName(),
                        'download_link' => route('backups.download', [
                            'disk' => $destination->diskName(),
                            'file' => $filename
                        ])
                    ];
                }
            }
        } catch (Exception $e) {
            // Handle cases where disks might not be reachable
        }

        return view('admin.backups.index', compact('backups'));
    }

    /**
     * Create a new backup.
     */
    public function create()
    {
        Gate::authorize('manage-backups');
        try {
            // Trigger backup in background or wait for it
            Artisan::call('backup:run', ['--only-db' => true]);
            return back()->with('success', 'Backup started successfully.');
        } catch (Exception $e) {
            return back()->with('error', 'Failed to start backup: ' . $e->getMessage());
        }
    }

    /**
     * Download a backup file.
     */
    public function download(Request $request)
    {
        Gate::authorize('manage-backups');
        $disk = $request->disk;
        $file = config('backup.backup.name') . '/' . $request->file;

        if (Storage::disk($disk)->exists($file)) {
            return Storage::disk($disk)->download($file);
        }

        return back()->with('error', 'Backup file not found.');
    }

    /**
     * Delete a backup file.
     */
    public function destroy(Request $request)
    {
        Gate::authorize('manage-backups');
        $disk = $request->disk;
        $file = config('backup.backup.name') . '/' . $request->file;

        if (Storage::disk($disk)->exists($file)) {
            Storage::disk($disk)->delete($file);
            return back()->with('success', 'Backup deleted successfully.');
        }

        return back()->with('error', 'Backup file not found.');
    }

    /**
     * Helper to format bytes to human-readable format.
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));

        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}
