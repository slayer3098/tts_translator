<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TranslationController;

Route::get('/', [TranslationController::class, 'index'])->name('translations.index');
Route::post('/translate', [TranslationController::class, 'translate'])->name('translations.translate');
Route::get('/history', [TranslationController::class, 'history'])->name('translations.history');
Route::get('/download/{id}', [TranslationController::class, 'downloadAudio'])->name('translations.download');