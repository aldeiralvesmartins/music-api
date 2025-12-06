<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Song;
use Illuminate\Support\Str;

class SongController extends Controller
{
    /**
     * Upload de música
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'file'  => 'required|mimes:mp3,wav,ogg,webm|max:10240',
            'category_id' => 'required|exists:categories,id',
        ]);

        $file = $request->file('file');
        $tmpPath = $file->getPathname();

        // Tamanho original
        $originalSize = round(filesize($tmpPath) / 1024 / 1024, 2);

        // Nome final
        $filename = time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.mp3';
        $finalPath = storage_path("app/public/songs/$filename");

        if (!file_exists(storage_path('app/public/songs'))) {
            mkdir(storage_path('app/public/songs'), 0755, true);
        }

        // Converter/comprimir (FFmpeg)
        $cmd = "ffmpeg -i " . escapeshellarg($tmpPath) . " -b:a 128k " . escapeshellarg($finalPath) . " -y";
        exec($cmd, $output, $returnVar);

        if ($returnVar !== 0) {
            return response()->json(['error' => 'Falha ao comprimir a música.'], 422);
        }

        $compressedSize = round(filesize($finalPath) / 1024 / 1024, 2);
        $url = asset("storage/songs/$filename");

        $song = Song::create([
            'title'    => $request->title,
            'filename' => $filename,
            'url'      => $url,
            'category_id' => $request->category_id,
        ]);

        return response()->json([
            'message' => "Upload concluído! Original: {$originalSize}MB → Final: {$compressedSize}MB",
            'song'    => $song,
        ], 201);
    }

    /**
     * Listar todas as músicas
     */
    public function index()
    {
        $songs = Song::with('category')->get()->map(function ($song) {
            $path = storage_path("app/public/songs/{$song->filename}");

            // Tamanho do arquivo
            $size = round(filesize($path) / 1024 / 1024, 2);

            // Duração via ffprobe
            $duration = null;
            $cmd = "ffprobe -i " . escapeshellarg($path) . " -show_entries format=duration -v quiet -of csv=\"p=0\"";
            $output = [];
            exec($cmd, $output, $returnVar);

            if ($returnVar === 0 && isset($output[0])) {
                $seconds = (float)$output[0];
                $duration = sprintf("%02d:%02d", floor($seconds / 60), $seconds % 60);
            }

            return [
                'id'         => $song->id,
                'title'      => $song->title,
                'filename'   => $song->filename,
                'url'        => $song->url,
                'category'   => $song->category ? [
                    'id' => $song->category->id,
                    'name' => $song->category->name,
                ] : null,
                'size_mb'    => $size,
                'duration'   => $duration,
                'created_at' => $song->created_at,
                'updated_at' => $song->updated_at,
            ];
        });

        return response()->json($songs);
    }

    /**
     * Mostrar dados da música
     */
    public function show($id)
    {
        $song = Song::with('category')->findOrFail($id);
        return response()->json($song);
    }

    /**
     * Tocar/baixar música
     */
    public function play($id)
    {
        $song = Song::findOrFail($id);
        return response()->file(storage_path("app/public/songs/" . $song->filename));
    }

    /**
     * Listar músicas por categoria
     */
    public function byCategory(Request $request, $category_id = null)
    {
        $id = $category_id ?? $request->query('category_id');

        $request->merge(['category_id' => $id]);
        $data = $request->validate([
            'category_id' => 'required|exists:categories,id',
        ]);

        $songs = Song::with('category')
            ->where('category_id', $data['category_id'])
            ->get()
            ->map(function ($song) {
                $path = storage_path("app/public/songs/{$song->filename}");

                $size = round(filesize($path) / 1024 / 1024, 2);

                $duration = null;
                $cmd = "ffprobe -i " . escapeshellarg($path) . " -show_entries format=duration -v quiet -of csv=\"p=0\"";
                $output = [];
                exec($cmd, $output, $returnVar);
                if ($returnVar === 0 && isset($output[0])) {
                    $seconds = (float)$output[0];
                    $duration = sprintf("%02d:%02d", floor($seconds / 60), $seconds % 60);
                }

                return [
                    'id'         => $song->id,
                    'title'      => $song->title,
                    'filename'   => $song->filename,
                    'url'        => $song->url,
                    'category'   => $song->category ? [
                        'id' => $song->category->id,
                        'name' => $song->category->name,
                    ] : null,
                    'size_mb'    => $size,
                    'duration'   => $duration,
                    'created_at' => $song->created_at,
                    'updated_at' => $song->updated_at,
                ];
            });

        return response()->json($songs);
    }
}
