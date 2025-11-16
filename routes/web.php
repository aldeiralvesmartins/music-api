<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/produto/{id}', function ($id) {
    $frontUrl = env('HOST_FRONT', 'https://commercefront.taskanalyzer.com');
    return redirect()->away($frontUrl . '/product/' . $id);
});
