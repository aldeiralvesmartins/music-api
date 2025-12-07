<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\RadioSession;
use App\Models\PlayedSong;

class RadioCleanup extends Command
{
    // Removido o '=30' e '=7', tratamento do padrão será no handle()
    protected $signature = 'radio:cleanup
        {--days : Number of days to keep played songs}
        {--inactive-days : Number of days to keep radio sessions}';

    protected $description = 'Cleanup old radio sessions and played songs history';

    public function handle(): int
    {
        // Aplicando valor padrão caso a opção não seja passada
        $days = (int) ($this->option('days') ?? 30);
        $inactiveDays = (int) ($this->option('inactive-days') ?? 7);

        $cutPlayed = now()->subDays(max(1, $days));
        $cutSession = now()->subDays(max(1, $inactiveDays));

        DB::transaction(function () use ($cutPlayed, $cutSession) {
            $playedDeleted = PlayedSong::query()
                ->where('played_at', '<', $cutPlayed)
                ->delete();

            $sessionsDeleted = RadioSession::query()
                ->where(function ($q) use ($cutSession) {
                    $q->whereNull('last_saved_at')
                        ->orWhere('last_saved_at', '<', $cutSession);
                })
                ->delete();

            Log::info('radio.cleanup', [
                'played_deleted' => $playedDeleted,
                'sessions_deleted' => $sessionsDeleted,
                'cut_played' => $cutPlayed->toDateTimeString(),
                'cut_session' => $cutSession->toDateTimeString(),
            ]);

            $this->info("Deleted $playedDeleted played_songs and $sessionsDeleted radio_sessions");
        });

        return self::SUCCESS;
    }
}
