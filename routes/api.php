<?php

use App\Http\Controllers\SpotifyController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => env('API_VERSION')], function () {
    Route::group(['controller' => AuthController::class, 'prefix' => 'auth'], function () {
        Route::post('register', 'register')->name('auth.register');
        Route::post('refresh', 'refresh')->name('auth.refresh');
        Route::post('logout', 'logout')->name('auth.logout');
        Route::post('login', 'login')->name('auth.login');
        Route::get('me', 'profile')->name('auth.me');
    });
    Route::group(['controller' => UserController::class, 'middleware' => 'auth:api'], function () {
        Route::delete('users/destroy/bulk', 'destroyBulk')->name('users.destroy.bulk');
        Route::post('users/{id}/avatar', 'avatar')->name('users.avatar');
        Route::resource('users', UserController::class);
    });
    Route::group(['controller' => SpotifyController::class], function () {
        Route::get('spotify', 'index')->name('spotify.index');
    });
});
