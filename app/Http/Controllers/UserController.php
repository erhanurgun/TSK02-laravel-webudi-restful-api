<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use App\Http\Requests\ImageRequest;
use App\Http\Requests\UserRequest;
use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
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
            'users' => User::orderBy('id', 'desc')->paginate(10, ['*'], 'page', $page)
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\UserRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(UserRequest $request)
    {
        $req = $request->validated();
        $req['password'] = bcrypt($request->password);
        $user = User::create($req);
        $user = User::where('email', $user->email)->first();
        return response()->json([
            'success' => 'Kullanıcı başarıyla oluşturuldu.',
            'user' => $user
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $id = User::find($id);
        if ($id) {
            return response()->json(['user' => $id], 200);
        } else {
            return response()->json(['error' => 'Kullanıcı bulunamadı!'], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\UserRequest $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(UserRequest $request, $id)
    {
        $user = User::find($id);
        if ($user) {
            $user->update($request->validated());
            return response()->json([
                'success' => 'Kullanıcı başarıyla güncellendi.',
                'user' => $user
            ], 200);
        } else {
            return response()->json(['error' => 'Kullanıcı bulunamadı!'], 404);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::find($id);
        if ($user) {
            if (isset($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
            $user->delete();
            return response()->json([
                'success' => 'Kullanıcı başarıyla silindi.',
                'user' => $user
            ], 200);
        } else {
            return response()->json(['error' => 'Kullanıcı bulunamadı!'], 404);
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroyBulk(Request $request)
    {
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
    }

    /**
     * Display a listing of the resource.
     *
     * @param int $id
     * @param \App\Http\Requests\ImageRequest $request
     * @return \Illuminate\Http\Response
     */
    public function avatar(ImageRequest $request)
    {
        $user = User::find($request->id);
        if ($user) {
            if (isset($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
            $user->avatar = $request->file('image')->store('uploads/users', 'public');
            $user->save();
            return response()->json([
                'success' => 'Kullanıcı avatarı başarıyla güncellendi.',
                'user' => $user
            ], 200);
        } else {
            return response()->json(['error' => 'Kullanıcı bulunamadı!'], 404);
        }
    }
}
