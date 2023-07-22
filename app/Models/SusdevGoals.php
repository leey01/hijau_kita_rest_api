<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class SusdevGoals extends Model
{
    use HasFactory;

    protected $hidden = [
        'created_at',
        'updated_at',
        'pivot',
        'id',
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
}
