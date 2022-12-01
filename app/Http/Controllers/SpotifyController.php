<?php

/*
 * https://api.spotify.com/v1/playlists/{playlist_id}/tracks call track-list use guzzlehttp/guzzle
 * .env file:
 * SPOTIFY_CLIENT_ID=your-spotify-client-id
 * SPOTIFY_CLIENT_SECRET=your-spotify-client-secret
 * SPOTIFY_REDIRECT_URI=your-spotify-redirect-uri
 * SPOTIFY_TRACK_LIST_URL=https://api.spotify.com/v1/playlists/:playlist_id/tracks?uris=spotify:track:36od5gfj11fNcwuFKK7x4J
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;

class SpotifyController extends Controller
{
    // just track-list
    public function index(Request $request)
    {
        $client = new Client();
        $response = $client->request('GET', env('SPOTIFY_TRACK_LIST_URL'), [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->getAccessToken(),
            ],
        ]);
        return json_decode($response->getBody()->getContents());
    }

    // get access token
    private function getAccessToken()
    {
        $client = new Client();
        $response = $client->request('POST', 'https://accounts.spotify.com/api/token', [
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




}
