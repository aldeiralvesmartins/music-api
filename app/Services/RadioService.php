<?php

namespace App\Services;

use App\Models\PlayedSong;
use App\Models\RadioSession;
use App\Models\Song;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class RadioService
{
    private int $queueTargetSize = 30;
    private int $adsEveryN = 5; // inserir 1 anúncio a cada N músicas, se houver

    public function createOrResumeSession(?string $userId, ?string $sessionToken, ?string $companyId = null): RadioSession
    {
        // Preferir retomar pela session_token (mesma aba/anônimo) e só então por user_id
        $query = RadioSession::query();
        if ($sessionToken) {
            $query->where('session_token', $sessionToken);
        } elseif ($userId) {
            $query->where('user_id', $userId);
        }
        if ($companyId) {
            $query->where('company_id', $companyId);
        }

        $session = $query->first();
        if ($session) {
            Log::info('radio.resume_session', [
                'session_id' => $session->id,
                'user_id' => $userId,
                'session_token' => $sessionToken,
            ]);
            return $session;
        }

        // criar nova sessão
        $session = new RadioSession([
            'id' => (string) Str::uuid(),
            'user_id' => $userId,
            'session_token' => $sessionToken,
            'company_id' => $companyId,
            'loop_strategy' => 'no_repeat',
            'current_track_position' => 0,
            'last_saved_at' => now(),
        ]);

        $session->play_queue = $this->generateQueue($userId, $sessionToken, $companyId);
        $session->save();
        Log::info('radio.create_session', [
            'session_id' => $session->id,
            'user_id' => $userId,
            'session_token' => $sessionToken,
        ]);

        return $session;
    }

    public function saveProgress(string $sessionId, ?string $trackId, float $position, array $playQueue): RadioSession
    {
        return DB::transaction(function () use ($sessionId, $trackId, $position, $playQueue) {
            /** @var RadioSession $session */
            $session = RadioSession::lockForUpdate()->findOrFail($sessionId);
            if (!is_null($trackId)) {
                $session->current_track_id = $trackId;
            }
            $session->current_track_position = max(0, (float) $position);
            // Atualiza fila somente se o cliente enviou itens (>0). Evita apagar fila no servidor por engano.
            if (is_array($playQueue) && count($playQueue) > 0) {
                // limitar tamanho da fila salva
                $session->play_queue = array_values(array_slice($playQueue, 0, $this->queueTargetSize));
            }
            $session->last_saved_at = now();
            $session->save();
            Log::info('radio.save_progress', [
                'session_id' => $session->id,
                'track_id' => $trackId,
                'position' => $session->current_track_position,
            ]);
            return $session;
        });
    }

    public function getNextItem(string $sessionId): ?array
    {
        return DB::transaction(function () use ($sessionId) {
            /** @var RadioSession $session */
            $session = RadioSession::lockForUpdate()->findOrFail($sessionId);
            $queue = $session->play_queue ?? [];

            // reabastecer fila se necessário
            if (count($queue) === 0) {
                $queue = $this->generateQueue($session->user_id, $session->session_token, $session->company_id);
            }

            $next = array_shift($queue);
            // carregar dados da música/anúncio
            if (!$next) {
                $session->play_queue = $queue;
                $session->current_track_id = null;
                $session->current_track_position = 0;
                $session->last_saved_at = now();
                $session->save();
                Log::info('radio.next_item', [
                    'session_id' => $session->id,
                    'next' => null,
                ]);
                return null;
            }

            $session->play_queue = $queue;
            $session->current_track_id = $next['type'] === 'song' ? $next['id'] : null;
            $session->current_track_position = 0;
            $session->last_saved_at = now();
            $session->save();
            Log::info('radio.next_item', [
                'session_id' => $session->id,
                'next' => $next,
            ]);

            if ($next['type'] === 'song') {
                $song = Song::query()->where('id', $next['id'])->first();
                if (!$song) {
                    return null;
                }
                $result = [
                    'type' => 'song',
                    'song' => $song,
                ];
                Log::info('radio.next_item_song', [
                    'session_id' => $session->id,
                    'song_id' => $song->id,
                ]);
                return $result;
            }

            if ($next['type'] === 'ad') {
                $ad = Song::query()->where('id', $next['id'])->first();
                if (!$ad) {
                    return null;
                }
                $result = [
                    'type' => 'ad',
                    'song' => $ad,
                ];
                Log::info('radio.next_item_ad', [
                    'session_id' => $session->id,
                    'song_id' => $ad->id,
                ]);
                return $result;
            }

            return null;
        });
    }

    public function markPlayed(?string $userId, ?string $sessionToken, string $songId, ?string $companyId = null, ?int $durationPlayed = null): void
    {
        DB::transaction(function () use ($userId, $sessionToken, $songId, $companyId, $durationPlayed) {
            PlayedSong::create([
                'user_id' => $userId,
                'session_token' => $sessionToken,
                'song_id' => $songId,
                'played_at' => now(),
                'duration_played' => $durationPlayed,
                'company_id' => $companyId,
            ]);
        });
    }

    private function generateQueue(?string $userId, ?string $sessionToken, ?string $companyId): array
    {
        // Buscar músicas não-anúncio primeiro, evitando repetidas recentes (90 dias)
        $recentCut = now()->subDays(90);
        $recentQuery = PlayedSong::query()
            ->when($userId, fn($q) => $q->where('user_id', $userId))
            ->when(!$userId && $sessionToken, fn($q) => $q->where('session_token', $sessionToken))
            ->where('played_at', '>=', $recentCut)
            ->pluck('song_id')
            ->unique()
            ->toArray();

        $songsQuery = Song::query()
            ->where('anuncio', false)
            ->when($companyId, fn($q) => $q->where(function($q2) use ($companyId) {
                $q2->whereNull('company_id')->orWhere('company_id', $companyId);
            }))
            ->when(count($recentQuery) > 0, fn($q) => $q->whereNotIn('id', $recentQuery))
            ->inRandomOrder()
            ->limit($this->queueTargetSize);

        $songs = $songsQuery->pluck('id')->toArray();

        // anúncios disponíveis
        $ads = Song::query()
            ->where('anuncio', true)
            ->when($companyId, fn($q) => $q->where('company_id', $companyId))
            ->inRandomOrder()
            ->limit(max(1, intdiv(max(1, count($songs)), $this->adsEveryN)))
            ->pluck('id')
            ->toArray();

        // intercalar anúncios 1 a cada N músicas
        $queue = [];
        $songCounter = 0;
        $adIndex = 0;
        foreach ($songs as $songId) {
            $queue[] = ['type' => 'song', 'id' => $songId];
            $songCounter++;
            if ($songCounter % $this->adsEveryN === 0 && isset($ads[$adIndex])) {
                $queue[] = ['type' => 'ad', 'id' => $ads[$adIndex]];
                $adIndex++;
            }
        }

        return $queue;
    }
}
