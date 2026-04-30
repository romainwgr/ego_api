<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

use Illuminate\Support\Facades\Schedule;
use App\Console\Commands\SyncZoteroItems;
use App\Console\Commands\SyncOceanGlidersMap;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');


Schedule::command(SyncZoteroItems::class)->daily();

// Maps OceanGliders for gliders deployments: run daily at 02:00.

Schedule::command(SyncOceanGlidersMap::class)->dailyAt('02:00');




