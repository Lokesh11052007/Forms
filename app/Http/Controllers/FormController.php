<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class FormController extends Controller
{
    public function showBuilder()
    {
        $user = auth()->user();
        $tableName = strtolower($user->username);
        $forms = [];

        if (Schema::hasTable($tableName)) {
            $forms = DB::table($tableName)->pluck('field_name')->toArray();
        }

        return view('form.builder', compact('user', 'forms'));
    }

    // public function storeField(Request $request)
    // {
    //     $request->validate([
    //         'field_name' => 'required|string|max:255',
    //         'url' => 'nullable|url',
    //     ]);

    //     $username = Auth::user()->username;
    //     $tableName = strtolower($username);

    //     DB::table($tableName)->insert([
    //         'field_name' => $request->field_name,
    //         'url' => $request->url,
    //     ]);

    //     return back()->with('success', 'Field added successfully! using StoreField');
    // }

    public function store(Request $request)
    {
        $request->validate([
            'form_title' => 'required|string|max:255',
            'fields' => 'required|json',
        ]);

        $username = auth()->user()->username;
        $tableName = strtolower($username);
        $formTitle = $request->form_title;
        $slug = str_replace([' ', '-'], '_', strtolower($formTitle));
        $responseTable = 'responses_' . $slug;

        DB::table($tableName)->insert([
            'field_name' => $formTitle,
        ]);

        if (!Schema::hasTable($responseTable)) {
            Schema::create($responseTable, function ($table) use ($request) {
                $table->id();

                foreach (json_decode($request->fields, true) as $field) {
                    $column = str_replace(' ', '_', strtolower($field['label']));

                    // Set column type based on field type
                    switch ($field['type'] ?? 'text') {
                        case 'textarea':
                            $table->text($column)->nullable();
                            break;
                        case 'date':
                            $table->date($column)->nullable();
                            break;
                        case 'radio':
                        case 'checkbox':
                            // For multiple choice fields, store as JSON
                            $table->json($column)->nullable();
                            break;
                        case 'number':
                            $table->integer($column)->nullable();
                            break;
                        case 'email':
                            $table->string($column)->nullable();
                            break;
                        default: // text and other types
                            $table->string($column)->nullable();
                    }
                }

                $table->timestamps();
            });
        }

        $formUrl = route('form.fill', ['username' => $username, 'slug' => $slug]);
        return back()->with('success', 'Form created successfully! Share this link: ' . $formUrl);
    }

    public function previewForm()
    {
        $username = Auth::user()->username;
        $tableName = strtolower($username);

        if (!Schema::hasTable($tableName)) {
            abort(404, 'Your form table does not exist.');
        }

        $fields = DB::table($tableName)->get();
        return view('form.preview', compact('fields'));
    }

    public function dashboard()
    {
        $user = auth()->user();
        $tableName = strtolower($user->username);

        $forms = [];
        $responses = [];

        if (Schema::hasTable($tableName)) {
            $forms = DB::table($tableName)->pluck('field_name')->toArray();

            foreach ($forms as $formName) {
                $slug = str_replace([' ', '-'], '_', strtolower($formName));
                $responseTable = 'responses_' . $slug;

                if (Schema::hasTable($responseTable)) {
                    $responses[] = [
                        'name' => $formName,
                        'count' => DB::table($responseTable)->count(),
                    ];
                }
            }
        }

        return view('form.dashboard', compact('user', 'forms', 'responses'));
    }

    public function viewResponses($form)
    {
        $slug = str_replace([' ', '-'], '_', strtolower($form));
        $tableName = 'responses_' . $slug;

        if (!Schema::hasTable($tableName)) {
            abort(404, 'Responses table not found.');
        }

        $responses = DB::table($tableName)->get();
        return view('form.responses', [
            'form' => $form,
            'formName' => $form, // 👈 add this
            'responses' => $responses
        ]);
    }

    public function showResponseForm($username, $slug)
    {
        $userTable = strtolower($username);
        $formTitle = str_replace(['_', '-'], ' ', ucwords($slug));
        $responseTable = 'responses_' . $slug;

        if (
            !Schema::hasTable($userTable) ||
            !DB::table($userTable)->where('field_name', $formTitle)->exists() ||
            !Schema::hasTable($responseTable)
        ) {
            abort(404, 'Form not found.');
        }

        // Get column types using Schema::getColumnType()
        $columns = Schema::getColumnListing($responseTable);

        $fields = [];
        foreach ($columns as $column) {
            // Skip system columns
            if (in_array($column, ['id', 'created_at', 'updated_at'])) {
                continue;
            }

            // Get column type
            $columnType = Schema::getColumnType($responseTable, $column);

            // Determine field type based on column type
            $type = $this->mapColumnTypeToFieldType($columnType);

            $fields[] = [
                'name' => $column,
                'type' => $type,
                'options' => null // You can add logic to retrieve options if needed
            ];
        }

        return view('form.fill', [
            'fields' => $fields,
            'username' => $username,
            'slug' => $slug,
            'formSlug' => $slug,
            'formTitle' => $formTitle
        ]);
    }

    /**
     * Map database column types to form field types
     */
    protected function mapColumnTypeToFieldType($columnType)
    {
        $mapping = [
            'string' => 'text',
            'text' => 'textarea',
            'date' => 'date',
            'datetime' => 'datetime-local',
            'timestamp' => 'datetime-local',
            'integer' => 'number',
            'bigint' => 'number',
            'float' => 'number',
            'double' => 'number',
            'decimal' => 'number',
            'boolean' => 'checkbox',
            'json' => 'checkbox', // Assuming JSON is used for multiple checkboxes
            // Add more mappings as needed
        ];

        return $mapping[$columnType] ?? 'text';
    }

    public function submitResponse(Request $request, $username, $slug)
    {
        $table = 'responses_' . $slug;

        if (!Schema::hasTable($table)) {
            abort(404, 'Form response table not found.');
        }

        $data = $request->except('_token');

        // Add timestamps manually
        $now = now();
        $data['created_at'] = $now;
        $data['updated_at'] = $now;

        DB::table($table)->insert($data);
        return back()->with('success', 'Response submitted successfully!');
    }

    public function deleteForm($form)
    {
        $username = auth()->user()->username;
        $userTable = strtolower($username);
        $formName = str_replace(['_', '-'], ' ', ucwords($form));
        $slug = str_replace([' ', '-'], '_', strtolower($form));
        $responseTable = 'responses_' . $slug;

        // 1. Delete from user's form table
        DB::table($userTable)->where('field_name', $formName)->delete();

        // 2. Drop response table if exists
        if (Schema::hasTable($responseTable)) {
            Schema::drop($responseTable);
        }

        return back()->with('success', 'Form deleted successfully.');
    }
}
