<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    use HasFactory;

    protected $fillable = [
        'activity_id',
        'name',
        'description',
        'video',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function quiz_question()
    {
        return $this->hasMany(QuizQuestion::class)->with('quiz_answer');
    }
}
