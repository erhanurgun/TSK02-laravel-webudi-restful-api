<?php

namespace App\Http\Controllers;

use App\Http\Resources\AuthResource;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\UserRequest;
use Illuminate\Http\Request;
use App\Models\User;

class AuthController extends Controller
{
    // login user

    /**
     * @OA\Post(
     *      path="/auth/login",
     *      tags={"Auth"},
     *      summary="Login user",
     *      description="Login user",
     *      operationId="login",
     *      @OA\Parameter(name="email", in="query", description="Email", required=true, example="demo@urgun.com.tr", @OA\Schema(type="string")),
     *      @OA\Parameter(name="password", in="query", description="Password", required=true, example="Demo1234!", @OA\Schema(type="string")),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="string", example="Giriş başarıyla gerçekleştirildi."),
     *              @OA\Property(property="user", ref="#/components/schemas/User"),
     *          ),
     *     ),
     *     @OA\Response(
     *          response=400,
     *          description="Bad request",
     *          @OA\JsonContent(
     *              @OA\Property(property="error", type="string", example="E-posta adresi veya şifre hatalı, lütfen kontrol edip tekrar deneyiniz!"),
     *          ),
     *     ),
     *     @OA\Response(
     *     response=401,
     *     description="Unauthorized",
     *          @OA\JsonContent(
     *              @OA\Property(
     *                  property="error", type="object",
     *                  @OA\Property(property="token", type="string", example="Token bulunamadı veya geçersiz!")
     *              ),
     *              @OA\Property(
     *                  property="status", type="object",
     *                  @OA\Property(property="message", type="string", example="Bu işlem için gerekli izinlere sahip değilsiniz!"),
     *                  @OA\Property(property="code", type="integer", example=401)
     *              ),
     *          ),
     *     ),
     *     @OA\Response(
     *          response=404,
     *          description="Not Found",
     *          @OA\JsonContent(
     *              @OA\Property(property="error", type="string", example="Girilen bilgilere ait herhangi bir kullanıcı bulunamadı!"),
     *          ),
     *     ),
     *     @OA\Response(
     *          response=422,
     *          description="Unprocessable Entity",
     *          @OA\JsonContent(
     *              @OA\Property(property="error", type="string", example="E-posta adresi veya şifre boş bırakılamaz!"),
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
                auth()->login($user, true);
                return response()->json([
                    'success' => 'Giriş işlemi başarıyla gerçekleştirildi!',
                    'data' => new AuthResource($user),
                ], 200);
            } else {
                return response()->json([
                    'message' => 'E-posta adresi veya şifre hatalı, lütfen kontrol edip tekrar deneyiniz!',
                ], 400);
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
     *              @OA\Property(property="success", type="string", example="Giriş başarıyla gerçekleştirildi."),
     *              @OA\Property(property="user", ref="#/components/schemas/User"),
     *          ),
     *     ),
     *     @OA\Response(
     *          response=500,
     *          description="Internal Server Error",
     *          @OA\JsonContent(
     *              @OA\Property(
     *                  property="error", type="string",
     *                  example="Kullanıcı kaydı oluşturulurken bir hata oluştu. Hata: SQLSTATE[23000]: Integrity constraint violation: ..."
     *              ),
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
            auth()->login($user, true);
            return response()->json([
                'success' => 'Kullanıcı kaydı başarıyla oluşturuldu.',
                'data' => new AuthResource($user)
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => "Kullanıcı kaydı oluşturulurken bir hata oluştu. Hata: " . $e->getMessage()
            ], 500);
        }
    }

    // logout user

    public function logout()
    {
        //
    }

    // refresh token

    public function refresh()
    {
        //
    }

    // user profile

    public function profile()
    {
        // giriş yapmış kullanıcıyı döndürür
        if (auth()->check()) {
            return response()->json([
                'success' => 'Kullanıcı bilgileri başarıyla getirildi.',
                'data' => new UserResource(auth()->user())
            ], 200);
        } else {
            return response()->json([
                'error' => 'Kullanıcı bilgileri getirilemedi. Lütfen tekrar giriş yapınız!'
            ], 401);
        }
    }
}
