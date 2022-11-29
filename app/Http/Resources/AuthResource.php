<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class AuthResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $tokenCreated = $this->createToken('authToken');
        return [
            'user' => [
                'id' => $this->id,
                'name' => $this->name,
                'email' => $this->email,
                'phone' => $this->phone,
                'avatar' => $this->avatar,
                'created_at' => Carbon::parse($this->created_at)->format('d.m.Y H:i:s'),
            ],
            'token' => [
                'type' => 'Bearer',
                'expires_at' => Carbon::parse($tokenCreated->token->expires_at)->format('d.m.Y H:i:s'),
                'access_key' => $tokenCreated->accessToken
            ]
        ];
    }
}
