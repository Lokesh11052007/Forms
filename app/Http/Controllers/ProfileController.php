<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class ProfileController extends Controller
{


    public function deleteAccount(Request $request)
    {
        $user = Auth::user();
        $username = $user->username;

        // Step 1: Get all form entries from user's table (which has same name as username)
        if (Schema::hasTable($username)) {
            $forms = DB::table($username)->get();

            foreach ($forms as $form) {
                $fieldData = json_decode($form->field_name, true);
                if (is_array($fieldData)) {
                    foreach ($fieldData as $field) {
                        if (!empty($field['name']) && Schema::hasTable($field['name'])) {
                            Schema::dropIfExists($field['name']);
                        }
                    }
                }
            }

            // Step 2: Drop the user’s table
            Schema::dropIfExists($username);
        }

        // Step 3: Delete the user from `users` table by username
        DB::table('users')->where('username', $username)->delete();

        // Step 4: Logout the user
        Auth::logout();

        // Step 5: Redirect
        return redirect()->route('login')->with('status', 'Your account and data have been deleted.');
    }
}
