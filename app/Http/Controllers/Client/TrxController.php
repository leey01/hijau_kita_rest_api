<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Event;
use App\Models\TrxActivity;
use App\Models\TrxEvent;
use App\Models\User;
use App\Models\Wishlist;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class TrxController extends Controller
{
    public function trxActivity(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'activity_id' => 'required|integer',
            'description' => 'string',
            'image' => 'file|image|mimes:jpeg,png,jpg',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // upload image
        $imageName = time() . $request->image->getClientOriginalName();
        $imagePath = 'image/trx_activity/' . $imageName;
        Storage::disk('public')->put($imagePath, file_get_contents($request->image));

        // activity
        $activity = Activity::find($request->activity_id);

        try {
            // store data to database
            $data = TrxActivity::create([
                'user_id' => $request->user()->id,
                'activity_id' => $request->activity_id,
                'description' => $request->description,
                'image' => $imagePath,
                'is_valid' => 1,
                'trx_activity_type' => $activity->activity_type,
                'point_earned' => $activity->reward,
            ]);

            // nambah poin dari activity
            $user = User::find($request->user()->id);
            $user->point = $user->point + $activity->reward;
            $user->save();

        } catch (QueryException $e) {
            return response()->json([
                'message' => "Failed create data",
                'data' => $e
            ], 401);
        }

        return response()->json([
            'status' => 'success',
            'data' => $data,
            'user' => $user,
        ]);
    }

    public function trxEvent(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'event_id' => 'required|integer',
            'code' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $event = Event::find($request->event_id);
        $user = User::find($request->user()->id);

        if (!$event) {
            return response()->json([
                'status' => 'error',
                'message' => 'Event not found',
            ], 404);
        }

        $today = Carbon::today();
        $maxRedeemDate = Carbon::parse($event->date_end)->addDay();

        if ($today->gte($event->date_end)) {
            if ($event->code == $request->code) {
                try {
                    $data = TrxEvent::create([
                        'user_id' => $request->user()->id,
                        'event_id' => $request->event_id,
                        'code' => $request->code,
                        'point_earned' => $event->reward,
                    ]);
                    $user->point = $user->point + $event->reward;
                    $user->save();
                } catch (QueryException $e) {
                    return response()->json([
                        'message' => "Failed create data",
                        'data' => $e
                    ], 401);
                }

                return response()->json([
                    'status' => 'success',
                    'data' => $data,
                ]);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Code is not valid',
                ], 422);
            }
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Redemption period not started yet.',
            ], 422);
        }
    }

    public function addRemoveActivityWishlist($id)
    {
        $wishlist = Wishlist::where('user_id', Auth::user()->id)
            ->where('activity_id', $id)
            ->first();

        if ($wishlist) {
            $wishlist->delete();

            return response()->json([
                'message' => 'deleted',
                'data' => $wishlist
            ]);
        } else {
            $data = Wishlist::create([
                'user_id' => Auth::user()->id,
                'activity_id' => $id
            ]);

            return response()->json([
                'message' => 'created',
                'data' => $data
            ]);
        }
    }
}
