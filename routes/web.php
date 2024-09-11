<?php

use App\Http\Controllers\GameController;
use Illuminate\Support\Facades\Route;

Route::get('/', [GameController::class, 'showForm'])->name('showForm');
Route::post('/fetch', [GameController::class, 'fetchData'])->name('fetchData');
Route::get('/export', [GameController::class, 'exportToXlsx'])->name('exportXlsx');
