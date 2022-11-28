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
     *      path="/api/v1/users",
     *      tags={"Users"},
     *      summary="Get list of users",
     *      description="Returns list of users",
     *      operationId="index",
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
    public function index(Request $request)
    {
        $page = $request->has('page') ? $request->page : 1;
        $perPage = $request->has('per_page') ? $request->per_page : 10;
        $search = $request->has('search') ? $request->search : '';
        $sort = $request->has('sort') ? $request->sort : 'id';
        $order = $request->has('order') ? $request->order : 'asc';
        $columns = ['id', 'name', 'email', 'phone', 'created_at', 'updated_at'];
        $users = User::where(function ($query) use ($search, $columns) {
            foreach ($columns as $column) {
                $query->orWhere($column, 'like', '%' . $search . '%');
            }
        })->orderBy($sort, $order)->paginate($perPage, ['*'], 'page', $page);
        if ($users->lastPage() < $page) {
            return response()->json([
                'error' => '?page=' . $page . ' sayfasında herhangi bir veri bulunamadı!'
            ], 404);
        }
        if ($users->count() == 0) {
            return response()->json([
                'error' => 'Aranan kriterlere uygun herhangi bir veri bulunamadı!'
            ], 404);
        }
        return response()->json([
            'success' => 'Veri(ler) başarıyla listelendi.',
            'found' => $users->total() . ' adet veri bulundu.',
            'users' => UserResource::collection($users)
        ], 200);
    }

    // swagger store user

    /**
     * @OA\Post(
     *      path="/api/v1/users",
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
                'success' => 'Veri kaydı başarıyla oluşturuldu.',
                'user' => new UserResource($user)
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'error' => "Veri kaydı oluşturulurken bir hata oluştu. Hata: " . $e->getMessage()
            ], 500);
        }
    }

    // swagger show user

    /**
     * @OA\Get(
     *      path="/api/v1/users/{id}",
     *      tags={"Users"},
     *      summary="Get user information",
     *      description="Returns user data",
     *      operationId="show",
     *      @OA\Parameter(name="id", description="User id", required=true, in="path", example="ff130212-b3e9-4417-8363-df0848c3abdf",
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
                'error' => 'Aradığınız ID ile ilgili herhangi bir veri bulunamadı!'
            ], 404);
        }
        return response()->json([
            'user' => new UserResource($user)
        ], 200);
    }

    // swagger update user

    /**
     * @OA\Put(
     *      path="/api/v1/users/{id}",
     *      tags={"Users"},
     *      summary="Update user information",
     *      description="Returns user data",
     *      operationId="update",
     *      @OA\Parameter(name="id", description="User id", required=true, in="path", example="ff130212-b3e9-4417-8363-df0848c3abdf",
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
            return response()->json([
                'error' => 'Güncellemeye çalıştığınız ID ile ilgili herhangi bir veri bulunamadı!'
            ], 404);
        }
        try {
            $user->update($request->validated());
            return response()->json([
                'success' => 'Veri başarıyla güncellendi.',
                'user' => new UserResource($user)
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => "Veri güncellenirken bir hata oluştu. Hata: " . $e->getMessage()
            ], 500);
        }
    }

    // swagger destroy user

    /**
     * @OA\Delete(
     *      path="/api/v1/users/{id}",
     *      tags={"Users"},
     *      summary="Delete user",
     *      description="Returns user data",
     *      operationId="destroy",
     *      @OA\Parameter(name="id", description="User id", required=true, in="path", example="ff130212-b3e9-4417-8363-df0848c3abdf",
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
            return response()->json([
                'error' => 'Silmeye çalıştığınız ID ile ilgili herhangi bir veri bulunamadı!'
            ], 404);
        }
        try {
            if (isset($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
            $user->delete();
            return response()->json([
                'success' => 'Veri başarıyla silindi.',
                'user' => new UserResource($user)
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => "Veri silinirken bir hata oluştu. Hata: " . $e->getMessage()
            ], 500);
        }
    }

    // swagger change avatar user

    /**
     * @OA\Post(
     *      path="/api/v1/users/{id}/avatar",
     *      tags={"Users"},
     *      summary="Change avatar user",
     *      description="Returns user data",
     *      operationId="avatar",
     *      @OA\Parameter(name="id", description="User id", required=true, in="query", example="ff130212-b3e9-4417-8363-df0848c3abdf",
     *          @OA\Schema(type="string")
     *      ),
     *       @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"image"},
     *                 allOf={
     *                     @OA\Schema(
     *                         @OA\Property(
     *                             description="Avatar",
     *                             property="image",
     *                             type="string",
     *                             format="binary",
     *                         )
     *                     )
     *                 }
     *             )
     *         )
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
    public function avatar(ImageRequest $request)
    {
        $user = User::find($request->id);
        if (!$user) {
            return response()->json([
                'error' => 'Avatar\'ını değiştirmeye çalıştığınız ID ile ilgili herhangi bir veri bulunamadı!'
            ], 404);
        }
        try {
            if (isset($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
            $user->avatar = $request->file('image')->store('uploads/users', 'public');
            $user->save();
            return response()->json([
                'success' => 'Avatar başarıyla değiştirildi.',
                'user' => $user
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => "Avatar güncellenirken bir hata oluştu. Hata: " . $e->getMessage()
            ], 500);
        }
    }

    // swagger destroyBulk user

    /**
     * @OA\Delete(
     *      path="/api/v1/users/destroy/bulk",
     *      tags={"Users"},
     *      summary="Delete bulk user",
     *      description="Returns user data",
     *      operationId="destroyBulk",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *               @OA\Property(
     *                  property="ids", type="array",
     *                  example={"ffc6d76e-0804-4b5c-8608-db0881b34a84", "ff130212-b3e9-4417-8363-df0848c3abdf"},
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
                    'success' => 'Veri(ler) başarıyla silindi.',
                ], 200);
            }
            return response()->json(['error' => 'Silmeye çalıştığınız veri(ler) bulunamadı!'], 404);
        } catch (\Exception $e) {
            return response()->json([
                'error' => "Veri(ler) silinirken bir hata oluştu. Hata: " . $e->getMessage()
            ], 500);
        }
    }
}
