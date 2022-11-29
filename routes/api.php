<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Auth\LoginController;

Route::post('v1/login', [LoginController::class, 'login']);
Route::group(['prefix' => 'v1', 'middleware' => 'auth:api'], function () {
    Route::resource('users', UserController::class);
    Route::delete('users/destroy/bulk', [UserController::class, 'destroyBulk'])->name('users.destroy.bulk');
    Route::post('users/{id}/avatar', [UserController::class, 'avatar'])->name('users.avatar');
});
