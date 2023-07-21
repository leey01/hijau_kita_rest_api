<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\User;

class HomeController extends Controller
{
    public function index()
    {
        $data['user'] = $this->profile();
        $data['wishlist'] = $this->activityWishlist();

        return response()->json([
            'message' => 'success',
            'data' => $data
        ]);
    }

    public function activityWishlist()
    {
        $data = User::with('wishlist')->find(auth()->user()->id);

        return $data->wishlist ?? '';
    }

    public function profile()
    {
        $data = User::find(auth()->user()->id);

        return $data ?? '';
    }
}
