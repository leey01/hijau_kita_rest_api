<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrxEvent extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'event_id',
        'description',
        'point_earned',
        'created_at',
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function event()
    {
        return $this->belongsTo(Event::class)->with('sub_category');
    }


}
