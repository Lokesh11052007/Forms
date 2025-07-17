<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FormResponse extends Model
{
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
        'birth_date',
        'response_data'
    ];

    protected $casts = [
        'multiple_choice' => 'array',
        'location' => 'array',
        'response_data' => 'array',
    ];

    public function form()
    {
        return $this->belongsTo(Form::class);
    }
}
