<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class Event extends Model
{
    use HasFactory;

    protected $hidden = [
        'created_at',
        'updated_at',
        'image',
        'code'
    ];
    protected $fillable = ['name', 'sub_category_id','description', 'image', 'date_start', 'date_end', 'code'];
    protected $appends = [
        'image_url',
        'is_done'
    ];
    public function getImageUrlAttribute()
    {
        if ($this->image == null) {
            return null;
        }
        return Storage::disk('public')->url($this->image);
    }
    public function scopeActive($query)
    {
        return $query->whereDate('date_end', '>=', now());
    }
    public function sub_category()
    {
        return $this->belongsTo(SubCategory::class, 'sub_category_id', 'id')->with('susdev_goals');
    }
    public function provision()
    {
        return $this->hasMany(EventProvision::class, 'event_id', 'id');
    }
//    is done attribute
    public function getIsDoneAttribute()
    {
        $user_id = auth()->user()->id;
        $event_id = $this->id;
        $is_done = DB::table('trx_events')
            ->where('user_id', $user_id)
            ->where('event_id', $event_id)
            ->first();
        if ($is_done) {
            return true;
        } else {
            return false;
        }
    }
}
