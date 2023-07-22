<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Badge;
use App\Models\TrxActivity;
use App\Models\TrxBadge;
use App\Models\User;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function index()
    {
        $data['user'] = $this->profile();
        $data['done_activities'] = $this->doneActivities();
        $data['my_badges'] = $this->myBadge();
        $data['list_badges'] = $this->listBadges();

        return response()->json([
            'message' => 'success',
            'data' => $data
        ]);
    }

    public function profile()
    {
        $data = User::find(auth()->user()->id);

        return $data ?? '';
    }

    public function doneActivities()
    {
        $data = TrxActivity::with('activity')
            ->where('user_id', auth()->user()->id)
            ->get();

        $groupedData = $data->groupBy(function ($item) {
            return $item->activity->sub_category->name;
        });

        $groupedDataWithCount = $groupedData->map(function ($group) {
            return [
                'sub_category' => $group->first()->activity->sub_category->name,
                'image_url' => $group->first()->activity->sub_category->image_url,
                'count' => $group->count(),
            ];
        })->sortByDesc('count')->values();

        return $groupedDataWithCount ?? '';
    }

    public function listBadges()
    {
        $badges = Badge::all();

        return $badges ?? '';
    }

    public function myBadge()
    {
        $badges = TrxBadge::with('badge')
            ->where('user_id', auth()->user()->id)
            ->get();

        $mapped = $badges->map(function ($badge) {
            return [
                'id' => $badge->badge->id,
                'name' => $badge->badge->name,
                'image_url' => $badge->badge->image_url,
                'price' => $badge->badge->price,
            ];
        });

        return $mapped ?? '';
    }
}
