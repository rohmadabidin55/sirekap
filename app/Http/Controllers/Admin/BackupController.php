<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Spatie\Backup\BackupDestination\Backup;
use Spatie\Backup\BackupDestination\BackupDestination;

class BackupController extends Controller
{
    public function index()
    {
        $disk = Storage::disk(config('backup.backup.destination.disks')[0]);
        $files = $disk->allFiles(config('backup.backup.name'));

        $backups = [];
        foreach ($files as $file) {
            if (substr($file, -4) === '.zip' && $disk->exists($file)) {
                $backups[] = [
                    'file_path' => $file,
                    'file_name' => str_replace(config('backup.backup.name') . '/', '', $file),
                    'file_size' => $this->formatSizeUnits($disk->size($file)),
                    'last_modified' => date('d-m-Y H:i:s', $disk->lastModified($file)),
                ];
            }
        }

        // Urutkan dari yang terbaru
        $backups = array_reverse($backups);

        return view('admin.backup.index', compact('backups'));
    }

    public function create()
    {
        try {
            // Jalankan perintah backup
            Artisan::call('backup:run', ['--only-db' => true]);
            return back()->with('status', 'Proses backup berhasil dimulai!');
        } catch (\Exception $e) {
            return back()->with('error', 'Proses backup gagal: ' . $e->getMessage());
        }
    }

    public function download($fileName)
    {
        $filePath = config('backup.backup.name') . '/' . $fileName;
        $disk = Storage::disk(config('backup.backup.destination.disks')[0]);

        if ($disk->exists($filePath)) {
            return $disk->download($filePath);
        }
        return back()->with('error', 'File backup tidak ditemukan.');
    }

    public function destroy($fileName)
    {
        $filePath = config('backup.backup.name') . '/' . $fileName;
        $disk = Storage::disk(config('backup.backup.destination.disks')[0]);

        if ($disk->exists($filePath)) {
            $disk->delete($filePath);
            return back()->with('status', 'File backup berhasil dihapus.');
        }
        return back()->with('error', 'File backup tidak ditemukan.');
    }

    private function formatSizeUnits($bytes)
    {
        if ($bytes >= 1073741824) {
            $bytes = number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            $bytes = number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            $bytes = number_format($bytes / 1024, 2) . ' KB';
        } elseif ($bytes > 1) {
            $bytes = $bytes . ' bytes';
        } elseif ($bytes == 1) {
            $bytes = $bytes . ' byte';
        } else {
            $bytes = '0 bytes';
        }
        return $bytes;
    }
}
