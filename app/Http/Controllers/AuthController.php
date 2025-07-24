<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Session;
use Illuminate\Database\Schema\Blueprint; // Missing import

class AuthController extends Controller
{
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'username' => 'required|alpha_dash|unique:users,username|max:255', // Added alpha_dash for better validation
            'email' => 'required|email|unique:users,email|max:255',
            'password' => 'required|min:6|confirmed', // Added password confirmation
        ]);

        // Start database transaction
        DB::beginTransaction();

        try {
            // Create the user
            $user = User::create([
                'username' => $request->username,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            // Create a table named after the username
            $tableName = strtolower($user->username);

            // Additional table name validation
            if (!preg_match('/^[a-z][a-z0-9_]*$/', $tableName)) {
                throw new \Exception('Invalid username for table name.');
            }

            // Check if table already exists (just in case)
            if (Schema::hasTable($tableName)) {
                throw new \Exception('Table already exists.');
            }

            Schema::create($tableName, function (Blueprint $table) {
                $table->id();
                $table->string('field_name');
                $table->timestamps();
            });

            DB::commit();

            return redirect()->route('login.form')->with('success', 'Registration successful! Please login.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Registration failed: ' . $e->getMessage()]);
        }
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);

        $credentials = $request->only('username', 'password');

        if (Auth::attempt($credentials, $request->remember)) { // Added remember me functionality
            $request->session()->regenerate();
            return redirect()->intended('dashboard'); // Using intended for better redirect
        }

        return back()->withErrors([
            'username' => 'Invalid credentials',
        ])->onlyInput('username');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login.form');
    }
}
