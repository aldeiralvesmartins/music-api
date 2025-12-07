<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\PlayedSong;
use App\Models\RadioSession;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('radio:cleanup', function () {
    $cutoff = now()->subDays(90);
    $sessionsCutoff = now()->subDays(7);

    $deletedPlays = PlayedSong::query()->where('played_at', '<', $cutoff)->delete();
    $deletedSessions = RadioSession::query()->where(function($q) use ($sessionsCutoff) {
        $q->whereNull('last_saved_at')->orWhere('last_saved_at', '<', $sessionsCutoff);
    })->delete();

    Log::info('radio.cleanup', [
        'deleted_plays' => $deletedPlays,
        'deleted_sessions' => $deletedSessions,
    ]);

    $this->info("Cleanup done. played_songs deleted: {$deletedPlays}, radio_sessions deleted: {$deletedSessions}");
})->purpose('Clean old radio sessions and played songs (TTL)');
