<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use App\Http\Resources\UserResource;
use App\Http\Requests\ImageRequest;
use App\Http\Requests\UserRequest;
use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    // swagger get all users

    /**
     * @OA\Get(
     *      path="/api/users",
     *      tags={"Users"},
     *      summary="Get all users",
     *      description="Returns users data",
     *      operationId="index",
     *      @OA\Parameter(name="page", description="Page number", required=false, in="query", example="1",
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="data", type="object"),
     *          )
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Not found",
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Internal Server Error",
     *          @OA\JsonContent(
     *               @OA\Property(property="error", type="string"),
     *          )
     *      ),
     * )
     */
    public function index()
    {
        // page/{number} değeri yoksa 1 değerini alır
        $page = request()->has('page') ? request('page') : 1;
        // paginate edilen sayfa da kayıt yoksa
        if (User::paginate(10)->lastPage() < $page) {
            return response()->json([
                'error' => '?page=' . $page . ' sayfasında herhangi bir kayıt bulunamadı!'
            ], 404);
        }
        return response()->json([
            'users' => UserResource::collection(User::orderBy('id', 'desc')->paginate(10, ['*'], 'page', $page))
        ], 200);
    }

    // swagger search users

    /**
     * @OA\Get(
     *      path="/api/users/search",
     *      tags={"Users"},
     *      summary="Search users",
     *      description="Returns users data",
     *      operationId="search",
     *      @OA\Parameter(name="page", description="Page number", required=false, in="query", example="1",
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\Parameter(name="per_page", description="Number of items per page", required=false, in="query", example="10",
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\Parameter(name="search", description="Search", required=false, in="query",
     *          @OA\Schema(type="string")
     *      ),
     *      @OA\Parameter(name="sort", description="Sort", required=false, in="query", example="name",
     *          @OA\Schema(type="string")
     *      ),
     *      @OA\Parameter(name="order", description="Order", required=false, in="query", example="asc",
     *          @OA\Schema(type="enum", enum={"asc", "desc"})
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="data", type="object"),
     *          )
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Not found",
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Internal Server Error",
     *          @OA\JsonContent(
     *               @OA\Property(property="error", type="string"),
     *          )
     *      ),
     * )
     */
    public function search(Request $request)
    {
        $page = $request->has('page') ? $request->page : 1;
        $perPage = $request->has('per_page') ? $request->per_page : 10;
        $search = $request->has('search') ? $request->search : '';
        $sort = $request->has('sort') ? $request->sort : 'id';
        $order = $request->has('order') ? $request->order : 'desc';
        $users = User::where('name', 'like', '%' . $search . '%')
            ->orWhere('email', 'like', '%' . $search . '%')
            ->orWhere('phone', 'like', '%' . $search . '%')
            ->orWhere('id', 'like', '%' . $search . '%')
            ->orderBy($sort, $order)
            ->paginate($perPage, ['*'], 'page', $page);
        if ($users->lastPage() < $page) {
            return response()->json([
                'error' => '?page=' . $page . ' sayfasında herhangi bir kayıt bulunamadı!'
            ], 404);
        }
        if ($users->count() == 0) {
            return response()->json([
                'error' => 'Aranan kriterlere uygun herhangi bir kayıt bulunamadı!'
            ], 404);
        }
        return response()->json([
            'success' => 'Kullanıcı(lar) başarıyla listelendi.',
            'found' => $users->total() . ' adet kullanıcı bulundu.',
            'users' => UserResource::collection($users)
        ], 200);
    }

    // swagger store user

    /**
     * @OA\Post(
     *      path="/api/users",
     *      tags={"Users"},
     *      summary="Store new user",
     *      description="Returns user data",
     *      operationId="store",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *               @OA\Property(property="name", type="string", example="Erhan ÜRGÜN"),
     *               @OA\Property(property="email", type="string", example="erhan@urgun.com.tr"),
     *               @OA\Property(property="password", type="string", example="Demo1234!"),
     *               @OA\Property(property="password_confirmation", type="string", example="Demo1234!"),
     *               @OA\Property(property="phone", type="string", example="+90 (542) 257 06 76"),
     *          )
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="data", type="object"),
     *          )
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Internal Server Error",
     *          @OA\JsonContent(
     *              @OA\Property(property="error", type="string"),
     *          )
     *      ),
     * )
     */
    public function store(UserRequest $request)
    {
        try {
            $req = $request->validated();
            $req['password'] = bcrypt($request->password);
            $user = User::create($req);
            $user = User::where('email', $user->email)->first();
            return response()->json([
                'success' => 'Kullanıcı başarıyla oluşturuldu.',
                'user' => new UserResource($user)
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'error' => "Kullanıcı oluşturulurken bir hata oluştu. Hata: " . $e->getMessage()
            ], 500);
        }
    }

    // swagger show user

    /**
     * @OA\Get(
     *      path="/api/users/{id}",
     *      tags={"Users"},
     *      summary="Get user information",
     *      description="Returns user data",
     *      operationId="show",
     *      @OA\Parameter(name="id", description="User id", required=true, in="path", example="ffc6d76e-0804-4b5c-8608-db0881b34a84",
     *          @OA\Schema(type="string")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="data", type="object"),
     *          )
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Not found",
     *      ),
     * )
     */
    public function show($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json([
                'error' => 'Aradığınız kullanıcı bulunamadı!'
            ], 404);
        }
        return response()->json([
            'user' => new UserResource($user)
        ], 200);
    }

    // swagger update user

    /**
     * @OA\Put(
     *      path="/api/users/{id}",
     *      tags={"Users"},
     *      summary="Update user information",
     *      description="Returns user data",
     *      operationId="update",
     *      @OA\Parameter(name="id", description="User id", required=true, in="path", example="ffc6d76e-0804-4b5c-8608-db0881b34a84",
     *          @OA\Schema(type="string")
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *               @OA\Property(property="name", type="string", example="Demo User"),
     *               @OA\Property(property="email", type="string", example="demo@urgun.com.tr"),
     *               @OA\Property(property="password", type="string", example="DemoUser1234!"),
     *               @OA\Property(property="password_confirmation", type="string", example="DemoUser1234!"),
     *               @OA\Property(property="phone", type="string", example="+90 (500) 000 00 00"),
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="data", type="object"),
     *          )
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Not found",
     *      ),
     *     @OA\Response(
     *          response=500,
     *          description="Internal Server Error",
     *          @OA\JsonContent(
     *               @OA\Property(property="error", type="string"),
     *          )
     *     ),
     * )
     */
    public function update(UserRequest $request, $id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['error' => 'Kullanıcı bulunamadı!'], 404);
        }
        try {
            $user->update($request->validated());
            return response()->json([
                'success' => 'Kullanıcı başarıyla güncellendi.',
                'user' => new UserResource($user)
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => "Kullanıcı güncellenirken bir hata oluştu. Hata: " . $e->getMessage()
            ], 500);
        }
    }

    // swagger destroy user

    /**
     * @OA\Delete(
     *      path="/api/users/{id}",
     *      tags={"Users"},
     *      summary="Delete user",
     *      description="Returns user data",
     *      operationId="destroy",
     *      @OA\Parameter(name="id", description="User id", required=true, in="path", example="ffc6d76e-0804-4b5c-8608-db0881b34a84",
     *          @OA\Schema(type="string")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="data", type="object"),
     *          )
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Not found",
     *      ),
     *     @OA\Response(
     *          response=500,
     *          description="Internal Server Error",
     *          @OA\JsonContent(
     *               @OA\Property(property="error", type="string"),
     *          )
     *     ),
     * )
     */
    public function destroy($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['error' => 'Kullanıcı bulunamadı!'], 404);
        }
        try {
            if (isset($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
            $user->delete();
            return response()->json([
                'success' => 'Kullanıcı başarıyla silindi.',
                'user' => new UserResource($user)
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => "Kullanıcı silinirken bir hata oluştu. Hata: " . $e->getMessage()
            ], 500);
        }
    }

    // swagger destroyBulk user

    /**
     * @OA\Delete(
     *      path="/api/users/destroy/bulk",
     *      tags={"Users"},
     *      summary="Delete bulk user",
     *      description="Returns user data",
     *      operationId="destroyBulk",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *               @OA\Property(
     *                  property="ids", type="array",
     *                  example={"ffc6d76e-0804-4b5c-8608-db0881b34a84", "ffc6d76e-0804-4b5c-8608-db0881b34a84"},
     *                  @OA\Items(type="string")),
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="data", type="object"),
     *          )
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Not found",
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Internal Server Error",
     *          @OA\JsonContent(
     *               @OA\Property(property="error", type="string"),
     *          )
     *      ),
     * )
     */
    public function destroyBulk(Request $request)
    {
        try {
            $ids = $request->ids;
            $users = User::whereIn('id', $ids)->get();
            if ($users && count($users) > 0) {
                foreach ($users as $user) {
                    if (isset($user->avatar)) {
                        Storage::disk('public')->delete($user->avatar);
                    }
                    $user->delete();
                }
                return response()->json([
                    'success' => 'Kullanıcı(lar) başarıyla silindi.',
                ], 200);
            }
            return response()->json(['error' => 'Kullanıcı(lar) bulunamadı!'], 404);
        } catch (\Exception $e) {
            return response()->json([
                'error' => "Kullanıcı(lar) silinirken bir hata oluştu. Hata: " . $e->getMessage()
            ], 500);
        }
    }

    // swagger change avatar user

    /**
     * @OA\Put(
     *      path="/api/users/change/avatar",
     *      tags={"Users"},
     *      summary="Change avatar user",
     *      description="Returns user data",
     *      operationId="changeAvatar",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *               @OA\Property(property="avatar", type="string", example="ffc6d76e-0804-4b5c-8608-db0881b34a84"),
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="data", type="object"),
     *          )
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Not found",
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Internal Server Error",
     *          @OA\JsonContent(
     *               @OA\Property(property="error", type="string"),
     *          )
     *      ),
     * )
     */
    public function changeAvatar(ImageRequest $request)
    {
        $user = User::find($request->id);
        if (!$user) {
            return response()->json(['error' => 'Kullanıcı bulunamadı!'], 404);
        }
        try {
            if (isset($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
            $user->avatar = $request->file('image')->store('uploads/users', 'public');
            $user->save();
            return response()->json([
                'success' => 'Kullanıcı avatarı başarıyla güncellendi.',
                'user' => $user
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => "Kullanıcı avatarı güncellenirken bir hata oluştu. Hata: " . $e->getMessage()
            ], 500);
        }
    }
}
