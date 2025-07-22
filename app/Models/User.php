<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

// Optional: only include if you use API or Sanctum
// use Laravel\Sanctum\HasApiTokens;

use App\Models\Form;
use App\Models\FormResponse;

class User extends Authenticatable
{
    // use HasApiTokens if you're using Sanctum, otherwise delete/comment it
    // use HasApiTokens, Notifiable;
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'username',
        'email',
        'password',
        // Uncomment these if you're using social login
        // 'provider',
        // 'provider_id',
        // 'avatar',
    ];

    /**
     * A user can own many forms.
     */
    public function forms()
    {
        return $this->hasMany(Form::class);
    }

    /**
     * A user can submit many responses (optional if you're tracking this).
     */
    public function responses()
    {
        return $this->hasMany(FormResponse::class);
    }
}
