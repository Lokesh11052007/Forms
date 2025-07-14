<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FormController extends Controller
{
    // Show form builder page
    public function showBuilder()
    {
        return view('form.builder');
    }

    // Save a field to the user's dynamic table
    public function storeField(Request $request)
    {
        $request->validate([
            'field_name' => 'required|string|max:255',
            'url' => 'nullable|url'
        ]);

        $username = Auth::user()->username;
        $tableName = strtolower($username);

        DB::table($tableName)->insert([
            'field_name' => $request->field_name,
            'url' => $request->url
        ]);

        return redirect()->back()->with('success', 'Field added successfully!');
    }
    public function previewForm()
    {
        $username = Auth::user()->username;
        $tableName = strtolower($username);

        // Check if table exists
        if (!Schema::hasTable($tableName)) {
            abort(404, "Your form table does not exist.");
        }

        // Get all fields (questions)
        $fields = DB::table($tableName)->get();

        return view('form.preview', compact('fields'));
    }


    public function publicForm($username)
    {
        $tableName = strtolower($username);

        // Check if the table exists
        if (!Schema::hasTable($tableName)) {
            abort(404, 'Form not found');
        }

        // Get form fields
        $fields = DB::table($tableName)->get();

        return view('form.public', compact('username', 'fields'));
    }


    public function dashboard()
    {
        $user = auth()->user();
        $userTable = strtolower($user->username);

        if (!Schema::hasTable($userTable)) {
            return view('dashboard', [
                'user' => $user,
                'forms' => [],
                'responses' => []
            ]);
        }

        // Step 1: Fetch form records from the user table
        $forms = DB::table($userTable)->pluck('field_name')->toArray();

        // Step 2: Check for response tables and count entries
        $responseData = [];

        foreach ($forms as $formName) {
            $slug = str_replace([' ', '-'], '_', strtolower($formName));
            $responseTable = 'responses_' . $slug;

            if (Schema::hasTable($responseTable)) {
                $count = DB::table($responseTable)->count();
                $responseData[] = [
                    'name' => $formName,
                    'count' => $count,
                ];
            }
        }

        return view('dashboard', [
            'user' => $user,
            'forms' => $forms,
            'responses' => $responseData
        ]);
    }
    public function viewResponses($form)
    {
        $user = auth()->user();
        $slug = str_replace([' ', '-'], '_', strtolower($form));
        $tableName = 'responses_' . $slug;

        if (!Schema::hasTable($tableName)) {
            abort(404, 'Responses table not found.');
        }

        $responses = DB::table($tableName)->get();

        return view('form.responses', [
            'formName' => $form,
            'responses' => $responses
        ]);
    }
}
