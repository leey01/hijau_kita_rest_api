<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\EmailVerificationNotification;
use Ichtrojan\Otp\Otp;
use Illuminate\Http\Request;

class EmailVerificationController extends Controller
{
    private $otp;

    public function __construct()
    {
        $this->otp = new Otp;
    }

    // make email verification function
    public function sendEmailVerification(Request $request)
    {
        $request->user()->notify(new EmailVerificationNotification());
        return response()->json([
            'status' => 'success',
            'message' => 'success send email verification',
        ], 200);
    }

    public function email_verification(Request $request)
    {
        $request->validate([
            'otp' => 'required',
        ]);

        // validate otp
        $otp2 = $this->otp->validate($request->user()->email, $request->otp);
        if (!$otp2->status) {
            return response()->json([
                'status' => 'error',
                'message' => $otp2->message,
            ], 401);
        }

        // verified email
        $user = User::where('email', $request->user()->email)->first();
        $user->email_verified_at = now();
        $user->save();

        return response()->json([
            'status' => 'success',
            'message' => 'success verify email',
        ], 200);
    }
}
