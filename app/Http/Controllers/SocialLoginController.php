<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;

class SocialLoginController extends Controller
{
    // Redirect to Google for authentication.
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    // Handle callback from Google.
    public function callback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            // Check if user already exists with google_id or email.
            $user = User::where('email', $googleUser->getEmail())
                        ->orWhere('provider_id', $googleUser->getId())
                        ->first();

            if (!$user) {
                // Generate a unique username (base on email prefix).
                $baseUsername = explode('@', $googleUser->getEmail())[0];
                $username = $baseUsername;
                $suffix = 1;

                while (User::where('username', $username)->exists()) {
                    $username = $baseUsername . $suffix++;
                }

                // Create new user (no per-user tables; just a user record)
                $user = User::create([
                    'username'     => $username,
                    'email'        => $googleUser->getEmail(),
                    'password'     => Hash::make(Str::random(16)), // Useless for Google, but required by Laravel
                    'provider'     => 'google',
                    'provider_id'  => $googleUser->getId(),
                    'avatar'       => $googleUser->getAvatar(),
                ]);
            }

            Auth::login($user);

            return redirect()->route('dashboard');
        } catch (\Exception $e) {
            return redirect()->route('login.form')
                ->withErrors(['msg' => 'Google login failed: ' . $e->getMessage()]);
        }
    }
}
