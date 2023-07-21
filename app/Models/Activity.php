<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class Activity extends Model
{
    use HasFactory;

    protected $hidden = [
        'created_at',
        'updated_at',
        'image',
        'pivot'
    ];

    protected $fillable = [
        'sub_category_id',
        'name',
        'description',
        'image',
        'status',
    ];

    protected $appends = [
        'image_url',
        'is_done',
        'is_wishlist'
    ];

    public function getImageUrlAttribute()
    {
        if ($this->image == null) {
            return null;
        }
        return Storage::disk('public')->url($this->image);
    }

    public function getIsDoneAttribute()
    {
        $user_id = auth()->user()->id;
        $activity_id = $this->id;
        $is_done = DB::table('trx_activities')
            ->where('user_id', $user_id)
            ->where('activity_id', $activity_id)
            ->whereDate('created_at', Carbon::now()->format('Y-m-d'))
            ->first();
        if ($is_done) {
            return true;
        } else {
            return false;
        }
    }

    public function getIsWishlistAttribute()
    {
        $user_id = auth()->user()->id;
        $activity_id = $this->id;
        $is_wishlist = DB::table('wishlists')
            ->where('user_id', $user_id)
            ->where('activity_id', $activity_id)
            ->first();
        if ($is_wishlist) {
            return true;
        } else {
            return false;
        }
    }

    public function sub_category()
    {
        return $this->belongsTo(SubCategory::class, 'sub_category_id', 'id')->with('susdev_goals');
    }

    public function provision()
    {
        return $this->hasMany(ActivityProvision::class, 'activity_id', 'id');
    }
}
