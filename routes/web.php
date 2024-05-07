<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginRegisterController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\PostController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::controller(LoginRegisterController::class)->group(function () {
    Route::get('/register', 'register')->name('register');
    Route::post('/store', 'store')->name('store');
    Route::get('/login', 'login')->name('login');
    Route::post('/login', 'login')->name('login');
    Route::post('/authenticate', 'authenticate')->name('authenticate');
    Route::get('/dashboard', 'dashboard')->name('dashboard');
    Route::post('/logout', 'logout')->name('logout');
});


// Route::resource('posts', PostController::class);

Route::group(['prefix' => 'posts'], function () {
    Route::get('/', [PostController::class, 'index'])->name('post.index');
    Route::get('/create', [PostController::class, 'create'])->name('post.create');
    Route::post('/', [PostController::class, 'store'])->name('post.store');
    Route::get('/{id}', [PostController::class, 'show'])->name('post.show');
    Route::get('/{id}/edit', [PostController::class, 'edit'])->name('post.edit');
    Route::put('/{id}', [PostController::class, 'update'])->name('post.update');
    Route::post('/{id}', [PostController::class, 'destroy'])->name('post.destroy');
});


Route::prefix('posts')->group(function () {
    Route::post('{id}/like', [LikeController::class, 'likePost'])->name('posts.like');
    Route::delete('{id}/like', [LikeController::class, 'unlikePost'])->name('posts.unlike');
    Route::get('{id}/like-count', [LikeController::class, 'countLikes'])->name('posts.likeCount');
});



