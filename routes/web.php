<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

Route::get('/', function () {
    return view('welcome');
});

Route::group(['prefix' => 'api'], function () {
    Route::resource('users', UserController::class);
    Route::delete('users/destroy/bulk', [UserController::class, 'destroyBulk'])->name('users.destroy.bulk');
    Route::post('users/avatar/{id}', [UserController::class, 'avatar'])->name('users.avatar');
});
