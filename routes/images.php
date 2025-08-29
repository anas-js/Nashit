<?php

use App\Http\Controllers\BoardsController;
use App\Http\Controllers\CoursesController;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| Images Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/






// Auth User
// Route::middleware(['auth'])->group(function () {

// });
Route::get('/course/{id}', [CoursesController::class,'image'])->where("id", "^[a-z0-9\-]{36}$");

Route::get('/board/{id}', [BoardsController::class,'image'])->where("id", "^[a-z0-9\-]{36}$");

