<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

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
            'username' => 'required|unique:users,username',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
        ]);

        // Create the user
        $user = User::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Create a table named after the username
        $tableName = strtolower($user->username); // Make sure it's lowercase

        // Sanitize table name (you can also slugify it)
        if (!preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $tableName)) {
            return response()->json(['error' => 'Invalid username for table name.'], 400);
        }

        DB::statement("
            CREATE TABLE $tableName (
                id INT AUTO_INCREMENT PRIMARY KEY,
                field_name VARCHAR(255),
                url TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ");

        return response()->json(['message' => 'User registered and table created!']);
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
