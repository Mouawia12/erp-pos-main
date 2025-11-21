<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class BackupController extends Controller
{
    public function download()
    {
        Artisan::call('db:backup');
        $output = Artisan::output();

        // Try to parse the last created path from output
        preg_match('/Backup created: (.*)/', $output, $matches);
        $path = $matches[1] ?? null;

        if ($path && File::exists($path)) {
            return response()->download($path);
        }

        return redirect()->back()->with('error', __('main.db_backup_failed'));
    }
}
