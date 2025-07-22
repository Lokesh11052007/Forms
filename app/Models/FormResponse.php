<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FormResponse extends Model
{
    // 👇 Mass-assignable fields
    protected $fillable = [
        'form_id',
        'name',
        'mobile_number',
        'email',
        'short_answer',
        'paragraph',
        'multiple_choice',
        'single_choice',
        'location',
        'file_upload',
        'age',
        'choice',
        'text_field',
        'rating',
        'date_answer',
        'ranking',
        'likert',
        'nps',
        'section',
        // Optional: 'user_id', 'ip_address'
    ];

    // 👇 Automatically cast these fields
    protected $casts = [
        'multiple_choice' => 'array',
        'location'        => 'array',
        'ranking'         => 'array',
        'likert'          => 'array',
        'date_answer'     => 'date',
    ];

    /** 🔁 Relationship: this response belongs to a form */
    public function form()
    {
        return $this->belongsTo(Form::class);
    }

    /** 🧍 Optional relationship: if tracking which user submitted */
    // public function user()
    // {
    //     return $this->belongsTo(User::class);
    // }
}
