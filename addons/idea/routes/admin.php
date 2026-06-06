<?php

use App\Addons\Idea\Controllers\Admin\IdeaCommentController;
use App\Addons\Idea\Controllers\Admin\IdeaPostController;
use App\Addons\Idea\Controllers\Admin\IdeaTagController;
use Illuminate\Support\Facades\Route;

Route::get('/', [IdeaPostController::class, 'index'])->name('posts.index');
Route::get('/posts/create', [IdeaPostController::class, 'create'])->name('posts.create');
Route::post('/posts', [IdeaPostController::class, 'store'])->name('posts.store');
Route::get('/posts/{idea}', [IdeaPostController::class, 'show'])->name('posts.show');
Route::put('/posts/{idea}', [IdeaPostController::class, 'update'])->name('posts.update');
Route::delete('/posts/{idea}', [IdeaPostController::class, 'destroy'])->name('posts.destroy');

Route::get('/comments', [IdeaCommentController::class, 'index'])->name('comments.index');
Route::delete('/comments/{comment}', [IdeaCommentController::class, 'destroy'])->name('comments.destroy');
Route::post('/comments/{comment}/restore', [IdeaCommentController::class, 'restore'])->name('comments.restore');

Route::get('/tags', [IdeaTagController::class, 'index'])->name('tags.index');
Route::post('/tags', [IdeaTagController::class, 'store'])->name('tags.store');
Route::put('/tags/{tag}', [IdeaTagController::class, 'update'])->name('tags.update');
Route::delete('/tags/{tag}', [IdeaTagController::class, 'destroy'])->name('tags.destroy');
