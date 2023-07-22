<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Google_Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class GoogleController extends Controller
{
    function verifyGoogleIdToken(Request $request)
    {
        $idToken = $request->input('id_token');
        $client = new Google_Client(['client_id' => env('GOOGLE_CLIENT_ID')]);
        $payload = $client->verifyIdToken($idToken);


        if ($payload) {
            $email = $payload['email'];

            $user = \App\Models\User::where('email', $email)->first();

            if (!$user) {
                $user = User::create([
//                    'provider_id' => $payload['sub'],
                    'provider_name' => 'google',
                    'name' => $payload['name'],
                    'email' => $payload['email'],
                    'password' => Hash::make('password'),
                    'avatar' => $payload['picture'],
                    'email_verified_at' => now()
                ]);

                $passportToken = $this->generatePassportToken($user->email);
            } else {
                $passportToken = $this->generatePassportToken($user->email);
            }
        } else {
            return response()->json([
                'message' => 'Invalid ID Token'
            ], 400);
        }

        return response()->json([
            'user' => $user,
            'passport_token' => $passportToken,
        ], 200);
    }
}
