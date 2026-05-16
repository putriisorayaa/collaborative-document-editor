<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DocumentController;

Route::get('/', function () {
    return redirect('/login');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/documents', [DocumentController::class, 'index']);
    Route::get('/documents/create', [DocumentController::class, 'create']);
    Route::post('/documents', [DocumentController::class, 'store']);
    Route::get('/documents/{document}/edit', [DocumentController::class, 'edit']);
    Route::put('/documents/{document}', [DocumentController::class, 'update']);
    Route::get('/documents/{document}/revisions', [DocumentController::class, 'revisions']);
});

require __DIR__.'/auth.php';