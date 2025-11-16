<?php

use App\Models\Product;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/produto/{id}/share', function($id) {
    $product = Product::with(['category', 'images', 'specifications'])
        ->findOrFail($id);
    return view('share', ['product' => $product]);
});
