<?php

use App\Http\Controllers\MediaController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// CRUD standar Media.
// ->parameters() wajib: Laravel menyingularkan "media" menjadi "medium",
// sehingga route model binding `Media $media` tidak akan cocok tanpa baris ini.
Route::apiResource('media', MediaController::class)
    ->parameters(['media' => 'media']);

// Pengguna (hanya baca).
Route::apiResource('users', UserController::class)->only(['index', 'show']);

// Custom action koleksi pengguna (mengubah relasi many-to-many).
Route::get('/users/{user}/collection', [UserController::class, 'collection']);
Route::post('/users/{user}/collection', [UserController::class, 'addToCollection']);
Route::delete('/users/{user}/collection/{media}', [UserController::class, 'removeFromCollection']);
