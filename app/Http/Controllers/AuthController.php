<?php

namespace App\Http\Controllers;

use App\Http\Resources\AuthResource;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\UserRequest;
use Illuminate\Http\Request;
use App\Models\User;

class AuthController extends Controller
{
    // login user

    /**
     * @OA\Post(
     *     path="/auth/login",
     *     tags={"Auth"},
     *     summary="Login user",
     *     description="Login user",
     *     operationId="login",
     *     @OA\Parameter(name="email", in="query", description="Email", required=true, example="demo@urgun.com.tr", @OA\Schema(type="string")),
     *     @OA\Parameter(name="password", in="query", description="Password", required=true, example="Demo1234!", @OA\Schema(type="string")),
     *     @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="status", type="string", example="success"),
     *              @OA\Property(property="message", type="string", example="User logged in successfully"),
     *          ),
     *     ),
     *     @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *          @OA\JsonContent(
     *              @OA\Property(property="status", type="string", example="error"),
     *              @OA\Property(property="message", type="string", example="Unauthenticated"),
     *          ),
     *     ),
     *     @OA\Response(
     *          response=422,
     *          description="Unprocessable Entity",
     *          @OA\JsonContent(
     *              @OA\Property(property="status", type="string", example="error"),
     *              @OA\Property(property="message", type="string", example="The given data was invalid"),
     *          ),
     *     ),
     * )
     */
    public function login(Request $request)
    {
        // not empty email and password
        if ($request->filled(['email', 'password'])) {
            // check the request if the use email and password is valid and verified
            $user = User::where('email', $request->email)->first();
            if ($user && Hash::check($request->password, $user->password)) {
                return response()->json([
                    'success' => 'Giriş işlemi başarıyla gerçekleştirildi!',
                    'data' => new AuthResource($user),
                ], 200);
            } else {
                return response()->json([
                    'message' => 'E-posta adresi veya şifre hatalı, lütfen kontrol edip tekrar deneyiniz!',
                ], 401);
            }
        } else {
            return response()->json(['message' => 'E-posta adresi veya şifre boş bırakılamaz!'], 422);
        }
    }

    // register user

    /**
     * @OA\Post(
     *     path="/auth/register",
     *     tags={"Auth"},
     *     summary="Register user",
     *     description="Register user",
     *     operationId="register",
     *     @OA\Parameter(name="name", in="query", description="Name", required=true, example="Demo", @OA\Schema(type="string")),
     *     @OA\Parameter(name="email", in="query", description="Email", required=true, example="demo@urgun.con.tr", @OA\Schema(type="string")),
     *     @OA\Parameter(name="password", in="query", description="Password", required=true, example="Demo123!", @OA\Schema(type="string")),
     *     @OA\Parameter(name="password_confirmation", in="query", description="Password Confirmation", required=true, example="Demo123!", @OA\Schema(type="string")),
     *     @OA\Parameter(name="phone", in="query", description="Phone", required=true, example="+90 (555) 555 55 55", @OA\Schema(type="string")),
     *     @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="status", type="string", example="success"),
     *              @OA\Property(property="message", type="string", example="User registered successfully"),
     *          ),
     *     ),
     *     @OA\Response(
     *          response=422,
     *          description="Unprocessable Entity",
     *          @OA\JsonContent(
     *               @OA\Property(property="status", type="string", example="error"),
     *               @OA\Property(property="message", type="string", example="The given data was invalid."),
     *          ),
     *     ),
     *     @OA\Response(
     *          response=500,
     *          description="Internal Server Error",
     *          @OA\JsonContent(
     *              @OA\Property(property="status", type="string", example="error"),
     *              @OA\Property(property="message", type="string", example="Internal Server Error"),
     *          ),
     *     ),
     * )
     */
    public function register(UserRequest $request)
    {
        // return (new \App\Http\Controllers\UserController)->store($request);
        try {
            $request->merge(['password' => Hash::make($request->password)]);
            $user = User::create($request->all());
            $user = User::where('email', $user->email)->first();
            return response()->json([
                'success' => 'Kullanıcı kaydı başarıyla oluşturuldu.',
                'data' => new AuthResource($user)
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'error' => "Kullanıcı kaydı oluşturulurken bir hata oluştu. Hata: " . $e->getMessage()
            ], 500);
        }
    }

    // logout user

    /**
     * @OA\Post(
     *     path="/auth/logout",
     *     tags={"Auth"},
     *     summary="Logout user",
     *     description="Logout user",
     *     operationId="logout",
     *     @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="status", type="string", example="success"),
     *              @OA\Property(property="message", type="string", example="User logged out successfully"),
     *          ),
     *     ),
     *     @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *          @OA\JsonContent(
     *              @OA\Property(property="status", type="string", example="error"),
     *              @OA\Property(property="message", type="string", example="Unauthenticated"),
     *          ),
     *     ),
     * )
     */
    public function logout()
    {
        //
    }

    // refresh token

    /**
     * @OA\Post(
     *     path="/auth/refresh",
     *     tags={"Auth"},
     *     summary="Refresh token",
     *     description="Refresh token",
     *     operationId="refresh",
     *     @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="status", type="string", example="success"),
     *              @OA\Property(property="message", type="string", example="Token refreshed successfully"),
     *          ),
     *     ),
     *     @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *          @OA\JsonContent(
     *              @OA\Property(property="status", type="string", example="error"),
     *              @OA\Property(property="message", type="string", example="Unauthenticated"),
     *          ),
     *     ),
     * )
     */
    public function refresh()
    {
        //
    }

    // user profile

    /**
     * @OA\Get(
     *     path="/auth/me",
     *     tags={"Auth"},
     *     summary="User profile",
     *     description="User profile",
     *     operationId="profile",
     *     @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="status", type="string", example="success"),
     *              @OA\Property(property="message", type="string", example="User profile"),
     *          ),
     *     ),
     *     @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *          @OA\JsonContent(
     *              @OA\Property(property="status", type="string", example="error"),
     *              @OA\Property(property="message", type="string", example="Unauthenticated"),
     *          ),
     *     ),
     * )
     */
    public function profile()
    {
        //
    }
}
