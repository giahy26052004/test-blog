<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CategoryController;

Route::get('/', function () {
    return view('welcome');
});



Route::get('/posts', [PostController::class, 'index'])->name('posts.index');
Route::get('/posts/data', [PostController::class, 'data'])->name('posts.data');
Route::post('/posts', [PostController::class, 'store'])->name('posts.store');
Route::get('/posts/{post}/edit', [PostController::class, 'edit'])->name('posts.edit');
Route::put('/posts/{post}', [PostController::class, 'update'])->name('posts.update');
Route::delete('/posts/{post}', [PostController::class, 'destroy'])->name('posts.destroy');
