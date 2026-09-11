<?php

use App\Http\Controllers\ProjectController;
use App\Http\Controllers\SearchController;
use Illuminate\Support\Facades\Route;

Route::view("/", "welcome")->name("home");
Route::get("/search", SearchController::class)->name("search");
Route::get('/project/{provider}/{id}', [ProjectController::class, 'show'])->name('project.show');
