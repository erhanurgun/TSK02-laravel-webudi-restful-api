<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['prefix' => '/v1'], function () {
    Route::resource('users', UserController::class);
    Route::delete('users/destroy/bulk', [UserController::class, 'destroyBulk'])->name('users.destroy.bulk');
    Route::post('users/{id}/avatar', [UserController::class, 'avatar'])->name('users.avatar');
});
