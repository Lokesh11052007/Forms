<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Form extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'questions',
        'header',
        'footer',
        'expires_at',
        'is_active',
        // Optional: 'slug' for cleaner, shareable URLs (see note below)
    ];

    protected $casts = [
        'questions'   => 'array',
        'header'      => 'array',
        'footer'      => 'array',
        'expires_at'  => 'datetime',
        'is_active'   => 'boolean',
    ];

    // Relationship: Form has many responses
    public function responses()
    {
        return $this->hasMany(FormResponse::class);
    }

    // Relationship: Form belongs to a User (you, the form creator)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Optional: For SEO-friendly URLs, use slug as identifier (add 'slug' to fillable and migration if needed)
    // public function getRouteKeyName()
    // {
    //     return 'slug';
    // }
}
