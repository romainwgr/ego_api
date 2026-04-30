<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class SyncOceanGlidersMap extends Command
{
    protected $signature   = 'maps:sync-oceangliders {--month= : Target month in YYYY-MM format (defaults to previous month)}';
    protected $description = 'Download and store the OceanGliders monthly map (PNG + PDF) from OceanOPS as a local backup.';

    const OCEANOPS_BASE = 'https://www.ocean-ops.org/share/OceanGliders/Maps/';
    const STORAGE_DIR   = 'maps/oceangliders';

    public function handle(): int
    {
        $month = $this->option('month') ?? now()->subMonth()->format('Y-m');

        if (!preg_match('/^\d{4}-\d{2}$/', $month)) {
            $this->error("Invalid month format: {$month}. Expected YYYY-MM.");
            return self::FAILURE;
        }

        foreach (['png', 'pdf'] as $ext) {
            $filename = "{$month}-og-countries-v2.{$ext}";
            $path     = self::STORAGE_DIR . '/' . $filename;

            if (Storage::disk('public')->exists($path)) {
                $this->info("Already stored: {$filename}");
                continue;
            }

            $url      = self::OCEANOPS_BASE . $filename;
            $response = Http::timeout(30)->get($url);

            if (!$response->successful()) {
                $this->warn("Could not download {$filename} (HTTP {$response->status()}). Will retry next run.");
                Log::warning("SyncOceanGlidersMap: failed to download {$url}", ['status' => $response->status()]);
                continue;
            }

            Storage::disk('public')->put($path, $response->body());
            $this->info("Stored: {$filename}");
            Log::info("SyncOceanGlidersMap: stored {$path}");
        }

        return self::SUCCESS;
    }
}
