<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AiReviewController;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', function () {
    return view('code-reviewer');
});

Route::post('/ai/get-review', [AiReviewController::class, 'review'])->name('ai_route');