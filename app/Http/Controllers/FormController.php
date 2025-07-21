<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use App\Models\Form;
use App\Models\FormResponse;

class FormController extends Controller
{
    public function showBuilder()
    {
        $user = Auth::user();
        return view('builder', compact('user'));
    }

    public function storeField(Request $request)
    {
        $user = Auth::user();
        $table = strtolower($user->username);

        if (!Schema::hasTable($table)) {
            return back()->with('error', 'Form table does not exist.');
        }

        DB::table($table)->insert([
            'field_name' => $request->field_name,
            'url' => $request->url ?? null,
            'created_at' => now(),
        ]);

        return redirect()->route('form.preview');
    }

    public function previewForm()
    {
        $user = Auth::user();
        $table = strtolower($user->username);

        $fields = DB::table($table)->get();

        return view('preview', compact('fields'));
    }

    public function builder()
    {
        return view('builder');
    }

    public function store(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $user = Auth::user();

        $form = Form::create([
               'user_id'     => $user->id,
        'title'       => $data['title'] ?? 'Untitled Form',
        'description' => $data['description'] ?? '',
        'questions'   => $data['questions'] ?? [],
        'header'      => $data['header'] ?? [],
        'footer'      => $data['footer'] ?? [],
        ]);

        return response()->json([
            'message' => 'Form created!',
            'form_id' => $form->id,
        ]);
    }

    public function dashboard()
    {
        $user = Auth::user();
        $username = strtolower($user->username);
        $tables = DB::select("SHOW TABLES");
        $key = array_keys((array) $tables[0])[0];

        $forms = [];
        $responses = [];

        foreach ($tables as $table) {
            $tableArray = (array) $table;
            $tableName = $tableArray[$key];

            if ($tableName === $username) continue;

            if (str_starts_with($tableName, 'responses_')) {
                $name = ucfirst(str_replace('responses_', '', $tableName));
                $count = DB::table($tableName)->count();
                $responses[] = ['name' => $name, 'count' => $count];
            } elseif ($tableName === $username) {
                $forms[] = $tableName;
            }
        }

        return view('dashboard', compact('user', 'forms', 'responses'));
    }

    public function respondViaUrl(Request $request)
    {
        $json = $request->query('data');
        $form = json_decode(urldecode($json), true);
        $formJson = $json;

        return view('responses', compact('form', 'formJson'));
    }

    public function storeResponse(Request $request)
    {
        $formData = json_decode($request->input('form_data'), true);
        $responses = $request->input('response');

        // 🛠 Find the form using title and description from form_data
        $form = Form::where('title', $formData['title'] ?? '')
                    ->where('description', $formData['description'] ?? '')
                    ->first();

        if (!$form) {
            return redirect()->back()->with('error', 'Form not found. Please check your form details.');
        }

        $response = new FormResponse();
        $response->form_id = $form->id;
        $response->response_data = $responses;

        foreach ($formData['questions'] as $index => $question) {
            $label = $question['type'];
            $value = $responses[$index] ?? null;

            if ($label === 'multiple_choice') {
                $response->$label = is_array($value) ? json_encode($value) : null;
            } elseif ($label === 'location') {
                $response->$label = json_encode($value);
            } elseif ($label === 'file') {
                if ($request->hasFile("response.$index")) {
                    $file = $request->file("response.$index");
                    $filename = time() . '_' . $file->getClientOriginalName();
                    $file->move(public_path('uploads'), $filename);
                    $response->file_upload = $filename;
                }
            } else {
                if (Schema::hasColumn('form_responses', $label)) {
                    $response->$label = $value;
                }
            }
        }

        $response->save();

        return redirect()->back()->with('success', 'Response submitted!');
    }

    public function showResponseForm($username, $slug)
    {
        $table = 'responses_' . strtolower($username);
        if (!Schema::hasTable($table)) {
            return redirect()->back()->with('error', 'Form not found.');
        }

        $formTitle = ucfirst(str_replace('_', ' ', $slug));
        $fields = DB::table($table)->pluck('field_name');

        return view('fill', compact('username', 'formSlug', 'formTitle', 'fields'));
    }

    public function submitResponse(Request $request, $username, $slug)
    {
        $table = 'responses_' . strtolower($username);
        if (!Schema::hasTable($table)) {
            return redirect()->back()->with('error', 'Form not found.');
        }

        $data = $request->except('_token');

        DB::table($table)->insert([
            'data' => json_encode($data),
            'created_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Response submitted!');
    }

    public function viewResponses($form)
    {
        $table = 'responses_' . strtolower($form);
        if (!Schema::hasTable($table)) {
            return back()->with('error', 'Form not found.');
        }

        $responses = DB::table($table)->get();

        return view('responses', compact('responses'));
    }

    public function deleteForm($form)
    {
        $table = 'responses_' . strtolower($form);
        if (Schema::hasTable($table)) {
            Schema::drop($table);
        }

        return back()->with('success', 'Form deleted.');
    }

    // TODO: exportExcel(), exportPdf()
}
