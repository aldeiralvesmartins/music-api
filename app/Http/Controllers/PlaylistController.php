<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Playlist;
use App\Models\Song;

class PlaylistController extends Controller
{
    public function index()
    {
        return response()->json(Playlist::withCount('songs')->get());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'company_id' => 'required|string|max:24',
        ]);

        $playlist = Playlist::create($data);
        return response()->json($playlist, 201);
    }

    public function show(string $id)
    {
        $playlist = Playlist::with('songs')->findOrFail($id);
        return response()->json($playlist);
    }

    public function update(Request $request, string $id)
    {
        $playlist = Playlist::findOrFail($id);
        $data = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'company_id' => 'sometimes|required|string|max:24',
        ]);
        $playlist->update($data);
        return response()->json($playlist);
    }

    public function destroy(string $id)
    {
        $playlist = Playlist::findOrFail($id);
        $playlist->delete();
        return response()->json(['message' => 'Playlist removida']);
    }

    public function addSongs(Request $request, string $id)
    {
        $playlist = Playlist::findOrFail($id);
        $data = $request->validate([
            'song_ids' => 'required|array',
            'song_ids.*' => 'string|exists:songs,id',
        ]);
        // montar matriz de attach com company_id no pivot
        $attachData = [];
        foreach ($data['song_ids'] as $songId) {
            $attachData[$songId] = ['company_id' => $playlist->company_id];
        }
        $playlist->songs()->syncWithoutDetaching($attachData);
        return response()->json($playlist->load('songs'));
    }

    public function removeSong(string $id, string $songId)
    {
        $playlist = Playlist::findOrFail($id);
        Song::findOrFail($songId);
        $playlist->songs()->detach($songId);
        return response()->json($playlist->load('songs'));
    }

    public function listSongs(string $id)
    {
        $playlist = Playlist::with(['songs' => function ($q) {
            $q->with('category');
        }])->findOrFail($id);

        $songs = $playlist->songs->map(function ($song) {
            return [
                'id' => $song->id,
                'title' => $song->title,
                'filename' => $song->filename,
                'url' => $song->url,
                'cover_url' => $song->cover_url,
                'anuncio' => $song->anuncio,
                'category' => $song->category ? [
                    'id' => $song->category->id,
                    'name' => $song->category->name,
                ] : null,
                'created_at' => $song->created_at,
                'updated_at' => $song->updated_at,
            ];
        });

        return response()->json([
            'id' => $playlist->id,
            'name' => $playlist->name,
            'description' => $playlist->description,
            'songs' => $songs,
        ]);
    }
}
