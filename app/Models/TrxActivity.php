<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrxActivity extends Model
{
    use HasFactory;

    protected $table = 'trx_activities';

    protected $fillable = [
        'user_id',
        'activity_id',
        'description',
        'image',
        'is_valid',
        'trx_activity_type',
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

    public function activity()
    {
        return $this->belongsTo(Activity::class)->with('sub_category');
    }
}
