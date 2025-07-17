<?php

namespace App\Models;

use App\Models\FormResponse;
use Illuminate\Database\Eloquent\Model;

class Form extends Model
{
    protected $fillable = ['title', 'description', 'questions'];
    protected $casts = ['questions' => 'array'];

    public function responses()
    {
        return $this->hasMany(FormResponse::class);
    }
}
