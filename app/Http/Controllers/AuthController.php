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

        Schema::create($tableName, function (Blueprint $table) {
            $table->id(); // This creates an auto-incrementing primary key 'id' column
            $table->string('field_name');
            $table->timestamps(); // Creates created_at and updated_at columns
        });

        return redirect('/login')->with('success', 'User registered successfully! Please login.');
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
