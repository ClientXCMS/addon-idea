<?php

use App\Addons\Idea\Controllers\Front\IdeaController;
use Illuminate\Support\Facades\Route;

Route::get('/', [IdeaController::class, 'index'])->name('index');
Route::get('/create', [IdeaController::class, 'create'])->middleware('auth')->name('create');
Route::post('/', [IdeaController::class, 'store'])->middleware('auth')->name('store');
Route::get('/{idea:slug}', [IdeaController::class, 'show'])->name('show');
Route::post('/{idea}/vote', [IdeaController::class, 'vote'])->middleware('auth')->name('vote');
Route::post('/{idea}/comments', [IdeaController::class, 'comment'])->middleware('auth')->name('comments.store');
Route::post('/{idea}/subscribe', [IdeaController::class, 'subscribe'])->middleware('auth')->name('subscribe');
