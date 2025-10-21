<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class UtilitiesController extends Controller
{
    public function systemCleanup()
    {
        $stats = [
            'activity_logs' => DB::table('activity_log')->count(),
            'old_sessions' => DB::table('sessions')->where('last_activity', '<', now()->subDays(30)->timestamp)->count(),
            'cache_size' => $this->getCacheSize(),
            'temp_files' => $this->getTempFilesCount(),
        ];

        return Inertia::render('Admin/Utilities/SystemCleanup', [
            'stats' => $stats
        ]);
    }

    public function performCleanup(Request $request)
    {
        $request->validate([
            'cleanup_type' => 'required|in:activity_logs,sessions,cache,temp_files'
        ]);

        $result = match($request->cleanup_type) {
            'activity_logs' => $this->cleanActivityLogs(),
            'sessions' => $this->cleanOldSessions(),
            'cache' => $this->clearCache(),
            'temp_files' => $this->cleanTempFiles(),
        };

        return redirect()->back()->with('success', $result['message']);
    }

    public function activityLogs(Request $request)
    {
        $query = DB::table('activity_log')
            ->orderBy('created_at', 'desc');

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('subject_type', 'like', "%{$search}%")
                  ->orWhere('causer_type', 'like', "%{$search}%");
            });
        }

        if ($request->has('date_from')) {
            $query->where('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->where('created_at', '<=', $request->date_to);
        }

        $logs = $query->paginate(50);

        return Inertia::render('Admin/Utilities/ActivityLogs', [
            'logs' => $logs,
            'filters' => $request->only(['search', 'date_from', 'date_to'])
        ]);
    }

    public function databaseStatus()
    {
        $tables = DB::select('SHOW TABLE STATUS');
        $totalSize = 0;
        $tableStats = [];

        foreach ($tables as $table) {
            $size = $table->Data_length + $table->Index_length;
            $totalSize += $size;
            $tableStats[] = [
                'name' => $table->Name,
                'rows' => $table->Rows,
                'size' => $this->formatBytes($size),
                'engine' => $table->Engine,
            ];
        }

        return Inertia::render('Admin/Utilities/DatabaseStatus', [
            'tables' => $tableStats,
            'total_size' => $this->formatBytes($totalSize),
            'database_name' => DB::getDatabaseName()
        ]);
    }

    public function optimizeDatabase()
    {
        $tables = DB::select('SHOW TABLES');
        $optimized = 0;

        foreach ($tables as $table) {
            $tableName = array_values((array)$table)[0];
            DB::statement("OPTIMIZE TABLE `{$tableName}`");
            $optimized++;
        }

        return redirect()->back()->with('success', "Optimized {$optimized} tables successfully.");
    }

    private function cleanActivityLogs()
    {
        $deleted = DB::table('activity_log')
            ->where('created_at', '<', now()->subDays(90))
            ->delete();

        return ['message' => "Deleted {$deleted} old activity log entries."];
    }

    private function cleanOldSessions()
    {
        $deleted = DB::table('sessions')
            ->where('last_activity', '<', now()->subDays(30)->timestamp)
            ->delete();

        return ['message' => "Deleted {$deleted} old session records."];
    }

    private function clearCache()
    {
        \Artisan::call('cache:clear');
        \Artisan::call('config:clear');
        \Artisan::call('view:clear');

        return ['message' => 'All caches cleared successfully.'];
    }

    private function cleanTempFiles()
    {
        $tempPath = storage_path('app/temp');
        $count = 0;

        if (is_dir($tempPath)) {
            $files = glob($tempPath . '/*');
            foreach ($files as $file) {
                if (is_file($file) && filemtime($file) < strtotime('-7 days')) {
                    unlink($file);
                    $count++;
                }
            }
        }

        return ['message' => "Deleted {$count} temporary files."];
    }

    private function getCacheSize()
    {
        $cachePath = storage_path('framework/cache');
        $size = 0;

        if (is_dir($cachePath)) {
            foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($cachePath)) as $file) {
                $size += $file->getSize();
            }
        }

        return $this->formatBytes($size);
    }

    private function getTempFilesCount()
    {
        $tempPath = storage_path('app/temp');
        
        if (!is_dir($tempPath)) {
            return 0;
        }

        return count(glob($tempPath . '/*'));
    }

    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $precision) . ' ' . $units[$i];
    }
}
