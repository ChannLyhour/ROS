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
use App\Helper\SystemHelper;
use App\Models\Setting;
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

        $scheduleEnabled = SystemHelper::getSetting('backup_schedule_enabled', '0');
        $scheduleTime = SystemHelper::getSetting('backup_schedule_time', '19:30');
        $backupDiskPath = SystemHelper::getSetting('backup_disk_path', storage_path('app'));

        return view('admin.backups.index', compact('backups', 'scheduleEnabled', 'scheduleTime', 'backupDiskPath'));
    }

    /**
     * Browse local folders on the server filesystem.
     */
    public function browseFolders(Request $request)
    {
        Gate::authorize('manage-backups');

        $requestedPath = $request->input('path');
        $isWindows = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';

        $drives = [];
        if ($isWindows) {
            foreach (range('C', 'Z') as $char) {
                $drive = $char . ':\\';
                if (@is_dir($drive)) {
                    $drives[] = $drive;
                }
            }
        }

        // Handle special "DRIVES" token on Windows to list drive letters
        if ($isWindows && $requestedPath === 'DRIVES') {
            return response()->json([
                'current_path' => 'DRIVES',
                'parent_path' => null,
                'folders' => array_map(function ($d) {
                    return ['name' => $d, 'path' => $d];
                }, $drives),
                'drives' => $drives,
                'is_windows' => true
            ]);
        }

        // Determine the target path
        if (empty($requestedPath)) {
            $currentPath = base_path();
        } else {
            // Standardize path separators for validation
            $target = $requestedPath;
            if ($isWindows) {
                $target = str_replace('/', '\\', $target);
            }
            $currentPath = @realpath($target);
            if (!$currentPath || !@is_dir($currentPath)) {
                // Fallback to absolute check if realpath fails
                if (@is_dir($target)) {
                    $currentPath = $target;
                } else {
                    $currentPath = base_path();
                }
            }
        }

        // Standardize Windows path separator and trailing slashes
        $currentPath = rtrim($currentPath, DIRECTORY_SEPARATOR);
        if ($isWindows && strlen($currentPath) === 2 && $currentPath[1] === ':') {
            $currentPath .= '\\';
        }

        $folders = [];
        try {
            if (@is_dir($currentPath) && @is_readable($currentPath)) {
                $files = scandir($currentPath);
                foreach ($files as $file) {
                    if ($file === '.' || $file === '..') {
                        continue;
                    }
                    $fullPath = $currentPath;
                    if (substr($fullPath, -1) !== DIRECTORY_SEPARATOR) {
                        $fullPath .= DIRECTORY_SEPARATOR;
                    }
                    $fullPath .= $file;

                    // Standardize slashes
                    $fullPath = preg_replace('~[\\\/]+~', DIRECTORY_SEPARATOR, $fullPath);

                    if (@is_dir($fullPath)) {
                        $folders[] = [
                            'name' => $file,
                            'path' => $fullPath
                        ];
                    }
                }
            }
        } catch (\Exception $e) {
            // Ignore directory reading issues
        }

        // Calculate parent path
        $parentPath = null;
        if ($isWindows) {
            if (strlen($currentPath) > 3) {
                $parentPath = dirname($currentPath);
                if (strlen($parentPath) === 2 && $parentPath[1] === ':') {
                    $parentPath .= '\\';
                }
            } else {
                $parentPath = 'DRIVES';
            }
        } else {
            if ($currentPath !== '/') {
                $parentPath = dirname($currentPath);
            }
        }

        return response()->json([
            'current_path' => $currentPath,
            'parent_path' => $parentPath,
            'folders' => $folders,
            'drives' => $drives,
            'is_windows' => $isWindows
        ]);
    }

    /**
     * Create a new folder inside the current directory.
     */
    public function createFolder(Request $request)
    {
        Gate::authorize('manage-backups');

        $parentPath = $request->input('parent_path');
        $folderName = $request->input('folder_name');

        if (empty($parentPath) || empty($folderName)) {
            return response()->json(['success' => false, 'message' => 'Invalid parameters.'], 400);
        }

        // Clean folder name to prevent directory traversal
        $folderName = basename($folderName);
        if (empty($folderName) || $folderName === '.' || $folderName === '..') {
            return response()->json(['success' => false, 'message' => 'Invalid folder name.'], 400);
        }

        $isWindows = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
        if ($isWindows) {
            $parentPath = str_replace('/', '\\', $parentPath);
        }

        $newPath = $parentPath;
        if (substr($newPath, -1) !== DIRECTORY_SEPARATOR) {
            $newPath .= DIRECTORY_SEPARATOR;
        }
        $newPath .= $folderName;

        try {
            if (!is_dir($parentPath)) {
                return response()->json(['success' => false, 'message' => 'Parent directory does not exist.'], 400);
            }
            if (is_dir($newPath)) {
                return response()->json(['success' => false, 'message' => 'Folder already exists.'], 400);
            }
            if (!is_writable($parentPath)) {
                return response()->json(['success' => false, 'message' => 'Parent directory is not writable.'], 400);
            }

            if (mkdir($newPath, 0755, true)) {
                return response()->json(['success' => true, 'message' => 'Folder created successfully.', 'path' => $newPath]);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }

        return response()->json(['success' => false, 'message' => 'Failed to create folder.'], 500);
    }

    /**
     * Update backup schedule settings.
     */
    public function updateSchedule(Request $request)
    {
        Gate::authorize('manage-backups');

        $request->validate([
            'backup_schedule_time' => 'required|date_format:H:i',
            'backup_disk_path' => ['nullable', 'string', function ($attribute, $value, $fail) {
                if ($value) {
                    $isWindows = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
                    $path = $value;
                    if ($isWindows) {
                        $path = str_replace('/', '\\', $path);
                    }
                    if (!is_dir($path)) {
                        try {
                            if (!mkdir($path, 0755, true) && !is_dir($path)) {
                                $fail('The backup folder path does not exist and could not be created.');
                                return;
                            }
                        } catch (\Exception $e) {
                            $fail('The backup folder path is invalid or cannot be created: ' . $e->getMessage());
                            return;
                        }
                    }
                    if (!is_writable($path)) {
                        $fail('The backup folder path is not writable. Please choose another location.');
                    }
                }
            }],
        ]);

        $enabled = $request->has('backup_schedule_enabled') ? '1' : '0';
        $time = $request->backup_schedule_time;
        $path = $request->backup_disk_path;

        Setting::updateOrCreate(
            ['key' => 'backup_schedule_enabled'],
            ['value' => $enabled]
        );
        SystemHelper::forgetSetting('backup_schedule_enabled');

        Setting::updateOrCreate(
            ['key' => 'backup_schedule_time'],
            ['value' => $time]
        );
        SystemHelper::forgetSetting('backup_schedule_time');

        if ($path) {
            $isWindows = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
            if ($isWindows) {
                $path = str_replace('/', '\\', $path);
            }
            $path = rtrim($path, '/\\');
            Setting::updateOrCreate(
                ['key' => 'backup_disk_path'],
                ['value' => $path]
            );
        } else {
            Setting::where('key', 'backup_disk_path')->delete();
        }
        SystemHelper::forgetSetting('backup_disk_path');

        return back()->with('success', 'Backup task scheduler settings updated successfully.');
    }

    /**
     * Create a new backup.
     */
    public function create()
    {
        Gate::authorize('manage-backups');
        try {
            Artisan::call('backup:run', ['--only-db' => true]);
            $output = Artisan::output();
            // Spatie outputs "Backup completed!" on success
            if (str_contains($output, 'failed') || str_contains($output, 'error') || str_contains($output, 'Error')) {
                return back()->with('error', 'Backup encountered an issue: ' . strip_tags($output));
            }
            return back()->with('success', 'Backup created successfully.');
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
        // Spatie stores backups inside a subdirectory named after config('backup.backup.name')
        $backupName = config('backup.backup.name');
        $file = $backupName . '/' . $request->file;

        if (Storage::disk($disk)->exists($file)) {
            return Storage::disk($disk)->download($file, $request->file);
        }

        // Also try the raw path in case file is at root of disk
        if (Storage::disk($disk)->exists($request->file)) {
            return Storage::disk($disk)->download($request->file);
        }

        return back()->with('error', 'Backup file not found. Expected at: ' . $file);
    }

    /**
     * Delete a backup file.
     */
    public function destroy(Request $request)
    {
        Gate::authorize('manage-backups');
        $disk = $request->disk;
        $backupName = config('backup.backup.name');
        $file = $backupName . '/' . $request->file;

        if (Storage::disk($disk)->exists($file)) {
            Storage::disk($disk)->delete($file);
            return back()->with('success', 'Backup deleted successfully.');
        }

        // Also try direct path
        if (Storage::disk($disk)->exists($request->file)) {
            Storage::disk($disk)->delete($request->file);
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
