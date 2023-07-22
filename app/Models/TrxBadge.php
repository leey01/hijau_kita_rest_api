<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrxBadge extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'badge_id',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function badge()
    {
        return $this->belongsTo(Badge::class, 'badge_id', 'id');
    }

}
