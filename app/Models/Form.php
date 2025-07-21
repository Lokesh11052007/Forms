<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Form extends Model
{
    protected $fillable = ['user_id', 'title', 'description', 'questions'];


    protected $casts = [
        'questions' => 'array',
    ];

    public function responses()
    {
        return $this->hasMany(FormResponse::class);
    }
}
