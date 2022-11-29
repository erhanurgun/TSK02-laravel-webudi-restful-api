<?php

namespace App\Http\Controllers\Auth;

use App\Http\Resources\LoginResource;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Models\User;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        // check the request if the use email and password is valid
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json(['message' => 'E-posta adresi veya şifre hatalı!'], 401);
        }
        // check the password
        if (Hash::check($request->password, $user->password)) {
            return response()->json(new LoginResource($user), 200);
        }
    }
}
