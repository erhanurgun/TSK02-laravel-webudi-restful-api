<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

Route::get('/', function () {
    return view('welcome');
});

Route::group(['prefix' => 'api'], function () {
    Route::get('users/search', [UserController::class, 'search'])->name('users.search');
    Route::resource('users', UserController::class);
    Route::delete('users/destroy/bulk', [UserController::class, 'destroyBulk'])->name('users.destroy.bulk');
    Route::post('users/{id}/avatar', [UserController::class, 'avatar'])->name('users.avatar');
});
