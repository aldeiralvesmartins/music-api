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
        try {
            // Validação
            $request->validate([
                'title' => 'required|string|max:255',
                'file'  => 'required|mimes:mp3,wav,ogg,webm|max:102400', // 100MB
                'category_id' => 'required|exists:categories,id',
            ]);

            $file = $request->file('file');

            if (!$file->isValid()) {
                return response()->json(['error' => 'Arquivo inválido ou corrompido.'], 422);
            }

            $tmpPath = $file->getPathname();
            $originalSize = round(filesize($tmpPath) / 1024 / 1024, 2);

            $filename = time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.mp3';
            $finalPath = storage_path("app/public/songs/$filename");

            // Cria pasta se não existir
            if (!file_exists(storage_path('app/public/songs'))) {
                if (!mkdir(storage_path('app/public/songs'), 0755, true)) {
                    return response()->json(['error' => 'Não foi possível criar a pasta de músicas.'], 500);
                }
            }

            // Comprimir música com FFmpeg
            $cmd = "ffmpeg -i " . escapeshellarg($tmpPath) . " -b:a 128k " . escapeshellarg($finalPath) . " -y 2>&1";
            exec($cmd, $output, $returnVar);

            if ($returnVar !== 0) {
                return response()->json([
                    'error' => 'Falha ao comprimir a música.',
                    'ffmpeg_output' => $output
                ], 422);
            }

            $compressedSize = round(filesize($finalPath) / 1024 / 1024, 2);
            $url = asset("storage/songs/$filename");

            // --- Extrair capa do MP3 ---
            $coverPath = storage_path("app/public/songs/covers/$filename.jpg");
            if (!file_exists(dirname($coverPath))) {
                mkdir(dirname($coverPath), 0755, true);
            }

            // FFmpeg para extrair a capa
            $cmdCover = "ffmpeg -i " . escapeshellarg($tmpPath) . " -an -vcodec copy " . escapeshellarg($coverPath) . " 2>&1";
            exec($cmdCover, $outputCover, $returnVarCover);

            $coverUrl = null;
            if ($returnVarCover === 0 && file_exists($coverPath)) {
                $coverUrl = asset("storage/songs/covers/$filename.jpg");
            }

            // Salvar no banco
            $song = Song::create([
                'title'       => $request->title,
                'filename'    => $filename,
                'url'         => $url,
                'cover_url'   => $coverUrl,
                'category_id' => $request->category_id,
            ]);

            return response()->json([
                'message' => "Upload concluído! Original: {$originalSize}MB → Final: {$compressedSize}MB",
                'song'    => $song,
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['error' => 'Erro de validação', 'details' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Ocorreu um erro inesperado.',
                'message' => $e->getMessage(),
                'trace' => $e->getTrace()
            ], 500);
        }
    }


    public function index()
    {
        $songs = Song::with('category')
            ->inRandomOrder() // Adicione esta linha
            ->get()
            ->map(function ($song) {
                $path = storage_path("app/public/songs/{$song->filename}");

                // Verifica se o arquivo existe
                if (!file_exists($path)) {
                    return [
                        'id'         => $song->id,
                        'title'      => $song->title,
                        'filename'   => $song->filename,
                        'url'        => $song->url,
                        'cover_url'  => $song->cover_url,
                        'category'   => $song->category ? [
                            'id' => $song->category->id,
                            'name' => $song->category->name,
                        ] : null,
                        'size_mb'    => 0,
                        'duration'   => '00:00',
                        'created_at' => $song->created_at,
                        'updated_at' => $song->updated_at,
                        'error'      => 'Arquivo não encontrado'
                    ];
                }

                // Tamanho do arquivo
                $size = round(filesize($path) / 1024 / 1024, 2);

                // Duração via ffprobe
                $duration = '00:00';
                $cmd = "ffprobe -i " . escapeshellarg($path) . " -show_entries format=duration -v quiet -of csv=\"p=0\" 2>&1";
                $output = [];
                exec($cmd, $output, $returnVar);

                if ($returnVar === 0 && isset($output[0])) {
                    $seconds = (float)$output[0];
                    $duration = sprintf("%02d:%02d", floor($seconds / 60), floor($seconds % 60));
                }

                return [
                    'id'         => $song->id,
                    'title'      => $song->title,
                    'filename'   => $song->filename,
                    'url'        => $song->url,
                    'cover_url'  => $song->cover_url,
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
