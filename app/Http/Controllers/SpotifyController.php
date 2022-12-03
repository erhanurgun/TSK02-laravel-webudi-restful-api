<?php

/*
 * https://api.spotify.com/v1/playlists/{playlist_id}/tracks call track-list use guzzlehttp/guzzle
 * .env file:
 * SPOTIFY_CLIENT_ID=your-spotify-client-id
 * SPOTIFY_CLIENT_SECRET=your-spotify-client-secret
 * SPOTIFY_REDIRECT_URI=your-spotify-redirect-uri
 * SPOTIFY_TRACK_LIST_URL=https://api.spotify.com/v1/playlists/:playlist_id/tracks?uris=spotify:track:36od5gfj11fNcwuFKK7x4J
 * SPOTIFY_TOKEN_URL=https://accounts.spotify.com/api/token
 * SPOTIFY_AUTHORIZE_URL=https://accounts.spotify.com/authorize
 * SPOTIFY_SCOPES="user-read-private user-read-email"
 * SPOTIFY_USER_PROFILE_URL=https://api.spotify.com/v1/me
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;

class SpotifyController extends Controller
{
    // access token
    private function accessToken()
    {
        $client = new Client();
        $response = $client->request('POST', env('SPOTIFY_TOKEN_URL'), [
            'headers' => [
                'Authorization' => 'Basic ' . base64_encode(env('SPOTIFY_CLIENT_ID') . ':' . env('SPOTIFY_CLIENT_SECRET')),
                'Content-Type' => 'application/x-www-form-urlencoded',
            ],
            'form_params' => [
                'grant_type' => 'client_credentials',
            ],
        ]);
        return json_decode($response->getBody()->getContents())->access_token;
    }

    // trackList

    /**
     * @OA\Get(
     *     path="/spotify/tracks",
     *     tags={"Spotify"},
     *     summary="Get track list",
     *     description="Get track list",
     *     operationId="trackList",
     *          @OA\Response(
     *              response=200,
     *              description="Successful operation",
     *              @OA\JsonContent(
     *                  type="object",
     *                  @OA\Property(property="success", type="boolean", example=true),
     *                  @OA\Property(property="data", type="object",
     *                  @OA\Property(
     *                      property="href", type="string",
     *                      example="https://api.spotify.com/v1/playlists/37i9dQZF1DXcBWIGoYBM5M/tracks?offset=0&limit=100&market=TR"
     *                  ),
     *              )
     *          )
     *      )
     * )
     */
    public function trackList(Request $request)
    {
        $client = new Client();
        $response = $client->request('GET', env('SPOTIFY_TRACK_LIST_URL'), [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->accessToken(),
            ],
        ]);
        return response()->json([
            'success' => true,
            'data' => json_decode($response->getBody()->getContents()),
        ]);
    }
}
