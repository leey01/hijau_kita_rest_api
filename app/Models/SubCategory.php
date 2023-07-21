<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class SubCategory extends Model
{
    use HasFactory;

    protected $hidden = [
        'created_at',
        'updated_at',
        'image'
    ];

    protected $appends = [
        'image_url',
    ];

    public function getImageUrlAttribute()
    {
        if ($this->image == null) {
            return null;
        }
        return Storage::disk('public')->url($this->image);
    }

    public function susdev_goals()
    {
        return $this->belongsToMany(SusdevGoals::class, 'sub_category_susdev', 'sub_category_id', 'susdev_id');
    }

    public function activities()
    {
        return $this->hasMany(Activity::class, 'sub_category_id', 'id')->with('sub_category');
    }
}
