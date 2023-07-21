<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\EmailVerificationNotification;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        // make validator
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8',
        ]);

        // check validator
        if ($validator->fails()) {
            // return error
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors(),
            ], 400);
        }

        // make user
        try {
            // make user
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);


//            dispatch(new SendOtpJob($user));
            // send email verification
            $user->notify(new EmailVerificationNotification());

            // make token
            $token = $user->createToken($user->name)->accessToken;

            // return success
            return response()->json([
                'status' => 'success',
                'message' => 'success register user',
                'data' => [
                    'user' => $user,
                    'token' => $token,
                ],
            ], 200);
        } catch (QueryException $e) {
            // return error
            return response()->json([
                'status' => 'error',
                'message' => $e->errorInfo,
            ], 400);
        }
    }

    public function login(Request $request)
    {
        // make validator
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // check validator
        if ($validator->fails()) {
            // return error
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors(),
            ], 400);
        }

        // login
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            // get user
            $user = Auth::user();

            // make token
            $token = $user->createToken($user->name)->accessToken;

            // return success
            return response()->json([
                'data' => [
                    'user' => $user,
                    'token' => $token,
                ],
            ], 200);
        } else {
            // return error
            return response()->json([
                'status' => 'error',
                'message' => 'login failed',
            ], 401);
        }
    }

    // logout with passport
    public function logout()
    {
        // get user
        $user = Auth::user();

        // revoke token
        $user->token()->revoke();

        // return success
        return response()->json([
            'status' => 'success',
            'message' => 'logout success',
        ], 200);
    }
}
