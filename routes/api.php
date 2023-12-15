<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\GenreController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/auth/register', [AuthController::class, 'createUser']);
Route::post('/auth/login', [AuthController::class, 'login']);

Route::get('/user/profile', [UserController::class, 'getProfile'])->middleware('auth:sanctum');

Route::resources(
    [
        'author' => AuthorController::class,
        'genre' => GenreController::class
    ],
    ['except' => ['create', 'edit']]
);

Route::get('/author/url_key/{url_key}', [AuthorController::class, 'showByUrlKey'])->name('author.showByUrlKey');
Route::get('/genre/url_key/{url_key}', [GenreController::class, 'showByUrlKey'])->name('genre.showByUrlKey');
