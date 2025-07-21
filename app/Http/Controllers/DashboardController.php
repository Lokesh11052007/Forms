<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\Form;

class DashboardController extends Controller
{
    public function __construct()
    {
        // ✅ Protect dashboard with auth middleware
        $this->middleware('auth');
    }

    public function dashboard()
    {
        // ✅ Get the logged-in user
        $user = Auth::user();

        // ✅ Get the user's forms
        $forms = Form::where('user_id', $user->id)->pluck('title')->toArray();

        // ✅ Count responses for each form
        $responses = collect($forms)->map(function ($formTitle) {
            // Build dynamic table name from form title
            $table = 'form_' . strtolower(str_replace([' ', '-', '.'], '_', $formTitle)) . '_responses';

            // Count rows if table exists
            $count = Schema::hasTable($table) ? DB::table($table)->count() : 0;

            return [
                'title' => $formTitle,
                'count' => $count,
            ];
        });

        // ✅ Render the dashboard view
        return view('dashboard', compact('user', 'forms', 'responses'));
    }
}
