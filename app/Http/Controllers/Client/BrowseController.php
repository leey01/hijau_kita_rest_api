<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Category;
use App\Models\Event;
use App\Models\Quiz;
use App\Models\SubCategory;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BrowseController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;
        $category_id = $request->category_id;
        $filteredActivities = [];
        $filteredSubCategories = [];
        $filteredEvents =[];
        $result = [];

        // get all data
        $activities = Activity::with('sub_category')
            ->orderBy('created_at', 'desc')
            ->get();


        $subCategories = SubCategory::orderBy('created_at', 'desc')
            ->get();


        if ($search && $category_id) {
            // Mengambil data aktivitas (activity) yang sesuai dengan kriteria pencarian
            $result['activities'] = Activity::with('sub_category')
                ->where('name', 'like', '%' . $search . '%')
                ->whereHas('sub_category', function ($query) use ($category_id) {
                    $query->whereIn('category_id', $category_id);
                })
                ->orderBy('created_at', 'desc')
                ->get();

            // Mengambil data sub-kategori (sub_category) yang sesuai dengan kriteria pencarian
            $result['sub_categories'] = SubCategory::whereIn('category_id', $category_id)
                ->orderBy('created_at', 'desc')
                ->get();


        } else if ($search) {
            $result['activities'] = Activity::with('sub_category')
                ->where('name', 'like', '%' . $search . '%')
                ->orderBy('created_at', 'desc')
                ->get();

            $result['sub_categories'] = SubCategory::where('name', 'like', '%' . $search . '%')
                ->orderBy('created_at', 'desc')
                ->get();


        } else if ($category_id) {
            foreach ($category_id as $id) {
                foreach ($activities as $activity) {
                    if ($activity->sub_category->category_id == $id) {
                        $filteredActivities[] = $activity;
                    }
                }
            }
            $result['activities'] = array_slice($filteredActivities, 0, 10);

            // ----------------------------------------------

            foreach ($category_id as $id) {
                foreach ($subCategories as $subCategory) {
                    if ($subCategory->category_id == $id) {
                        $filteredSubCategories[] = $subCategory;
                    }
                }
            }
            $result['sub_categories'] = array_slice($filteredSubCategories, 0, 3);

        } else {
            $result['activities'] = $activities->take(5);
            $result['sub_categories'] = $subCategories->take(5);
        }

        return response()->json([
            'status' => 'success',
            'data' => $result,
        ]);
    }

    public function listCategories()
    {
        $data = Category::all();

        return response()->json([
            'status' => 'success',
            'data' => $data,
        ]);
    }

    public function listSubCategories()
    {
        $data = SubCategory::all();

        return response()->json([
            'status' => 'success',
            'data' => $data,
        ]);
    }

    public function latestActivities(Request $request)
    {
        $category_id = $request->category_id;

        $activities = Activity::with('sub_category')
            ->orderBy('created_at', 'desc')
            ->get();

        $filteredActivities = [];

        if ($category_id) {
            foreach ($category_id as $id) {
                foreach ($activities as $activity) {
                    if ($activity->sub_category->category_id == $id) {
                        $filteredActivities[] = $activity;
                    }
                }
            }
        } else {
            $filteredActivities = $activities;
        }

        return response()->json([
            'status' => 'success',
            'activities' => $filteredActivities
        ]);
    }

    public function detailSubKategory($id)
    {
        $kategori = SubCategory::with(['susdev_goals', 'activities'])->find($id);

        return response()->json([
            'message' => 'success',
            'data' => $kategori
        ]);
    }

    public function detailActivity($id)
    {
        $activity = Activity::with(['sub_category', 'provision'])->find($id);

        return response()->json([
            'message' => 'success',
            'data' => $activity
        ]);
    }

    public function detailQuiz($id)
    {
        $quiz = Quiz::with('quiz_question')
            ->where('activity_id', $id)
            ->first();

        return response()->json([
            'message' => 'success',
            'data' => $quiz
        ]);
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
