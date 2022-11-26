<?php

namespace App\Http\Controllers;

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
        return response()->json([
            'user_count' => User::count(),
            'users' => User::all()
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

        return response()->json([
            'message' => 'Kullanıcı başarıyla oluşturuldu.',
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
            return response()->json(['message' => 'Kullanıcı bulunamadı!'], 404);
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
        $id = User::find($id);
        if ($id) {
            $id->update($request->validated());
            return response()->json([
                'message' => 'Kullanıcı başarıyla güncellendi.',
                'user_count' => User::count(),
                'user' => $id
            ], 200);
        } else {
            return response()->json(['message' => 'Kullanıcı bulunamadı!'], 404);
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
        $id = User::find($id);
        if ($id) {
            $id->delete();
            return response()->json([
                'message' => 'Kullanıcı başarıyla silindi.',
                'user' => $id
            ], 200);
        } else {
            return response()->json(['message' => 'Kullanıcı bulunamadı!'], 404);
        }
    }
}
