<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    AuthController,
    CompanyController,
    CategoryController,
    SongController,
    UserController,
    PlaylistController};
use App\Http\Controllers\Admin\AdminController;


Route::middleware('handler.exception')->group(function () {
// Public routes
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);

// Company routes (public)
    Route::post('/companies', [CompanyController::class, 'store'])->name('companies.store');


// Protected routes (require authentication)
    Route::middleware('auth:sanctum')->group(function () {
        // Shipping calculation (requires authentication to get user address)
// Category routes (public)
        Route::apiResource('categories', CategoryController::class)->only(['index', 'show']);
        Route::prefix('songs')->group(function () {
            Route::get('/', [SongController::class, 'index']);           // Listar músicas
            Route::get('/next', [SongController::class, 'next']);        // Próximo lote por token
            Route::post('/', [SongController::class, 'store']);          // Upload
            Route::get('by-category', [SongController::class, 'byCategory']); // Listar por categoria (query: category_id)
            Route::get('by-category/{category_id}', [SongController::class, 'byCategory'])->whereNumber('category_id'); // Listar por categoria (path param)
            Route::get('{id}', [SongController::class, 'show']);         // Ver dados da música
            Route::get('{id}/play', [SongController::class, 'play']);    // Tocar/baixar
        });

        // Playlists
        Route::prefix('playlists')->group(function () {
            Route::get('/', [PlaylistController::class, 'index']);
            Route::post('/', [PlaylistController::class, 'store']);
            Route::get('{id}', [PlaylistController::class, 'show']);
            Route::put('{id}', [PlaylistController::class, 'update']);
            Route::delete('{id}', [PlaylistController::class, 'destroy']);

            Route::get('{id}/songs', [PlaylistController::class, 'listSongs']);
            Route::post('{id}/songs', [PlaylistController::class, 'addSongs']);
            Route::delete('{id}/songs/{songId}', [PlaylistController::class, 'removeSong']);
        });
        // Auth routes
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/user', [AuthController::class, 'user']);
        Route::get('/me', [AuthController::class, 'user']); // Alias for /user to match frontend
        Route::put('/profile', [UserController::class, 'updateProfile']);
        Route::put('/change-password', [UserController::class, 'changePassword']);

        Route::post('/upload-image', function (\Illuminate\Http\Request $request) {
            $urls = [];

            foreach ($request->file('images', []) as $image) {
                $path = $image->storePublicly('products', 's3');
                $urls[] = \Illuminate\Support\Facades\Storage::disk('s3')->url($path);
            }

            return response()->json(['urls' => $urls]);
        });


        Route::prefix('categories')->group(function () {
            Route::get('/', [CategoryController::class, 'index']);    // Listar categorias
            Route::post('/', [CategoryController::class, 'store']);   // Criar categoria
            Route::get('{id}', [CategoryController::class, 'show']);  // Ver categoria
            Route::put('{id}', [CategoryController::class, 'update']); // Atualizar categoria
            Route::delete('{id}', [CategoryController::class, 'destroy']); // Remover categoria
        });


        // Admin routes
        Route::prefix('admin')->middleware(['auth:sanctum', 'admin'])->group(function () {
            // Dashboard
            Route::get('/dashboard', [AdminController::class, 'dashboard']);

            // User management
            Route::get('/users', [AdminController::class, 'listUsers']);
            Route::put('/users/{id}', [AdminController::class, 'updateUser']);
            Route::delete('/users/{id}', [AdminController::class, 'deleteUser']);
        });
    });
});
