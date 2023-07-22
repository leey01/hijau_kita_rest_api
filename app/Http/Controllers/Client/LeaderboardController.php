<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\TrxActivity;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LeaderboardController extends Controller
{
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'time' => 'required|in:last-week,today,all-time'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => 'failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $data['my_point'] = $this->myPoint();
        $data['leaderboard'] = $this->leaderboard($request->time);

        return response()->json([
            'message' => 'success',
            'data' => $data
        ]);
    }

    public function myPoint()
    {
        $data = User::find(auth()->user()->id);
        $result = [
            'name' => $data->name,
            'point' => $data->point,
            'avatar_url' => $data->avatar_url
        ];

        return $data ?? '';
    }

    public function leaderboard($param)
    {
        // Mendapatkan tanggal mulai dan akhir minggu lalu
        $startOfWeekLastWeek = Carbon::now()->subWeek()->startOfWeek();
        $endOfWeekLastWeek = Carbon::now()->subWeek()->endOfWeek();

        $time = $param;

        // last week & today
        if ($time == 'last-week') {
            $data = TrxActivity::with('user')
                ->whereBetween('created_at', [$startOfWeekLastWeek, $endOfWeekLastWeek])
                ->selectRaw('user_id, sum(point_earned) as total_point')
                ->groupBy('user_id')
                ->orderBy('total_point', 'desc')
                ->get();
        } else if ($time == 'today') {
            $data = TrxActivity::with('user')
                ->whereDate('created_at', now()->today())
                ->selectRaw('user_id, sum(point_earned) as total_point')
                ->groupBy('user_id')
                ->orderBy('total_point', 'desc')
                ->get();
        } else if ($time == 'all-time') {
            // alltime
            $data = TrxActivity::with('user')
                ->selectRaw('user_id, sum(point_earned) as total_point')
                ->groupBy('user_id')
                ->orderBy('total_point', 'desc')
                ->get();
        } else {
            $data = [];
        }

        return $data ?? '';
    }
}
