<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'username' => 'required|alpha_dash|unique:users,username|max:63', // PostgreSQL has 63-char limit for identifiers
            'email' => 'required|email|unique:users,email|max:255',
            'password' => 'required|min:6|confirmed',
        ]);

        DB::beginTransaction();

        try {
            $user = User::create([
                'username' => $request->username,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            $tableName = strtolower($user->username);

            // Additional validation for PostgreSQL table names
            if (!preg_match('/^[a-z][a-z0-9_]*$/', $tableName) || strlen($tableName) > 63) {
                throw new \Exception('Invalid username for table name.');
            }

            if (Schema::hasTable($tableName)) {
                throw new \Exception('Table already exists.');
            }

            // PostgreSQL-compatible schema creation
            Schema::create($tableName, function (Blueprint $table) {
                $table->id(); // This will use SERIAL for PostgreSQL
                $table->string('field_name');
                $table->text('url')->nullable();
                $table->timestamps(); // Creates both created_at and updated_at
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

        $user = \App\Models\User::where('username', $request->username)->first();

        if ($user && Hash::check($request->password, $user->password)) {
            Auth::login($user);
            return redirect()->route('dashboard'); // or dashboard
        }

        return back()->withErrors(['username' => 'Invalid username or password']);
    }

    // public function logout()
    // {
    //     Auth::logout();
    //     Session::flush();
    //     return redirect('/login');
    // }
    public function logout(Request $request)
    {
        auth()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login.form'); // adjust this if your login route name is different
    }
}
