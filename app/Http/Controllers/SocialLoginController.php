<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Schema;

class SocialLoginController extends Controller
{
    /**
     * Redirect to Google for authentication.
     */
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle callback from Google.
     */
    public function callback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            // Check if user already exists
            $user = User::where('email', $googleUser->getEmail())->first();

            if (!$user) {
                // Create a unique username if needed
                $baseUsername = explode('@', $googleUser->getEmail())[0];
                $username = $baseUsername;
                $suffix = 1;

                while (User::where('username', $username)->exists()) {
                    $username = $baseUsername . $suffix++;
                }

                $user = User::create([
                    'username' => $username,
                    'email' => $googleUser->getEmail(),
                    'password' => Hash::make(Str::random(16)), // random password
                ]);

                // Optional: create personal form table like "responses_username"
                $tableName = 'responses_' . strtolower($username);
                if (!Schema::hasTable($tableName)) {
                    Schema::create($tableName, function ($table) {
                        $table->id();
                        $table->json('data')->nullable();
                        $table->timestamps();
                    });
                }
            }

            Auth::login($user);
            return redirect()->route('dashboard');
        } catch (\Exception $e) {
            return redirect()->route('login.form')->withErrors(['msg' => 'Google login failed.']);
        }
    }
}
