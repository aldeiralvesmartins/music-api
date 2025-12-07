<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\RadioSession;
use App\Services\RadioService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RadioController extends Controller
{
    public function __construct(private RadioService $radioService)
    {
    }

    public function session(Request $request)
    {
        try {
            $user = $request->user();
            // Fallback: tentar autenticar via Bearer token (Sanctum) mesmo em rota pública
            if (!$user) {
                $bearer = $request->bearerToken();
                if ($bearer) {
                    $pat = \Laravel\Sanctum\PersonalAccessToken::findToken($bearer);
                    if ($pat && $pat->tokenable instanceof \App\Models\User) {
                        $user = $pat->tokenable;
                    }
                }
            }
            $userId = $user?->id;
            $sessionToken = $this->getSessionToken($request);
            $companyId = $user?->company_id;

            $session = $this->radioService->createOrResumeSession($userId, $sessionToken, $companyId);

            // Se o usuário está autenticado e a sessão ainda não possui user_id, anexar agora
            if ($user && !$session->user_id) {
                $session->user_id = $user->id;
                if (!$session->company_id) {
                    $session->company_id = $user->company_id;
                }
                $session->save();
            }

            return response()->json([
                'status' => 'ok',
                'data' => [
                    'session' => $session,
                ],
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function saveProgress(Request $request)
    {
        $validated = $request->validate([
            'session_id' => 'required|string',
            'track_id' => 'nullable|string',
            'position' => 'required|numeric|min:0',
            'play_queue' => 'present|array',
        ]);

        try {
            $session = RadioSession::findOrFail($validated['session_id']);
            $this->assertOwnership($request, $session);

            // Se o usuário está autenticado e a sessão ainda não possui user_id, anexar agora
            $user = $request->user();
            if (!$user) {
                $bearer = $request->bearerToken();
                if ($bearer) {
                    $pat = \Laravel\Sanctum\PersonalAccessToken::findToken($bearer);
                    if ($pat && $pat->tokenable instanceof \App\Models\User) {
                        $user = $pat->tokenable;
                    }
                }
            }
            if ($user && !$session->user_id) {
                $session->user_id = $user->id;
                if (!$session->company_id) {
                    $session->company_id = $user->company_id;
                }
                $session->save();
            }

            $updated = $this->radioService->saveProgress(
                $session->id,
                $validated['track_id'] ?? null,
                (float) $validated['position'],
                $validated['play_queue']
            );

            return response()->json([
                'status' => 'ok',
                'data' => ['session' => $updated],
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['status' => 'error', 'error' => $e->errors()], 422);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['status' => 'error', 'error' => 'Session not found'], 404);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'error' => $e->getMessage()], 500);
        }
    }

    public function next(Request $request)
    {
        $validated = $request->validate([
            'session_id' => 'required|string',
        ]);

        try {
            $session = RadioSession::findOrFail($validated['session_id']);
            $this->assertOwnership($request, $session);

            // Se o usuário está autenticado e a sessão ainda não possui user_id, anexar agora
            $user = $request->user();
            if (!$user) {
                $bearer = $request->bearerToken();
                if ($bearer) {
                    $pat = \Laravel\Sanctum\PersonalAccessToken::findToken($bearer);
                    if ($pat && $pat->tokenable instanceof \App\Models\User) {
                        $user = $pat->tokenable;
                    }
                }
            }
            if ($user && !$session->user_id) {
                $session->user_id = $user->id;
                if (!$session->company_id) {
                    $session->company_id = $user->company_id;
                }
                $session->save();
            }

            $item = $this->radioService->getNextItem($session->id);

            return response()->json([
                'status' => 'ok',
                'data' => [
                    'item' => $item,
                ],
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['status' => 'error', 'error' => $e->errors()], 422);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['status' => 'error', 'error' => 'Session not found'], 404);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'error' => $e->getMessage()], 500);
        }
    }

    public function markPlayed(Request $request)
    {
        $validated = $request->validate([
            'session_id' => 'nullable|string',
            'song_id' => 'required|string',
            'duration_played' => 'nullable|integer|min:0',
        ]);

        try {
            $user = $request->user();
            if (!$user) {
                $bearer = $request->bearerToken();
                if ($bearer) {
                    $pat = \Laravel\Sanctum\PersonalAccessToken::findToken($bearer);
                    if ($pat && $pat->tokenable instanceof \App\Models\User) {
                        $user = $pat->tokenable;
                    }
                }
            }
            $userId = $user?->id;
            $sessionToken = $this->getSessionToken($request);
            $companyId = $user?->company_id;

            if (!empty($validated['session_id'])) {
                $session = RadioSession::findOrFail($validated['session_id']);
                $this->assertOwnership($request, $session);
                $sessionToken = $sessionToken ?: $session->session_token;
                $companyId = $companyId ?: $session->company_id;
                $userId = $userId ?: $session->user_id;
            }

            $this->radioService->markPlayed(
                $userId,
                $sessionToken,
                $validated['song_id'],
                $companyId,
                $validated['duration_played'] ?? null
            );

            return response()->json(['status' => 'ok', 'data' => true]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['status' => 'error', 'error' => $e->errors()], 422);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'error' => $e->getMessage()], 500);
        }
    }

    private function getSessionToken(Request $request): ?string
    {
        $token = $request->header('X-Session-Token')
            ?: $request->query('session_token');
        if ($token && Str::isUuid($token)) {
            return $token;
        }
        return null;
    }

    private function assertOwnership(Request $request, RadioSession $session): void
    {
        $user = $request->user();
        $token = $this->getSessionToken($request);

        $userOwns = $user && $session->user_id === $user->id;
        $tokenOwns = $token && $session->session_token === $token;

        if (!$userOwns && !$tokenOwns) {
            abort(403, 'Forbidden: session ownership mismatch');
        }
    }
}
