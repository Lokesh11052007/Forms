<?php

namespace App\Http\Controllers;

use App\Models\Form;
use App\Models\FormResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FormController extends Controller
{
    // Show JSON form builder UI
    public function builder()
    {
        return view('builder');
    }

    // Store a new custom form built by user
    public function store(Request $request)
    {
        $user = Auth::user();

        $form = Form::create([
            'user_id'     => $user->id,
            'title'       => $request->title ?? 'Untitled Form',
            'description' => $request->description ?? '',
            'header'      => $request->header ?? [],
            'footer'      => $request->footer ?? [],
            'questions'   => $request->questions ?? [],
            'expires_at'  => $request->expires_at ?? null,
            'is_active'   => true,
        ]);

        return response()->json([
            'message' => 'Form created!',
            'form_id' => $form->id,
        ]);
    }

    // Show form fill page by ID
    public function showForm($id)
    {
        $form = Form::findOrFail($id);

        if ($form->expires_at && now()->greaterThan($form->expires_at)) {
            return view('form_expired');
        }

        return view('fill', [
            'form' => $form,
            'formJson' => json_encode($form),
        ]);
    }

    // ✅ Store submitted form responses safely
    public function storeResponse(Request $request)
    {
        // ✅ Validation
        $data = $request->validate([
            'form_id'         => 'required|exists:forms,id',
            'name'            => 'nullable|string|max:255',
            'mobile_number'   => 'nullable|string|max:20',
            'email'           => 'nullable|email',
            'short_answer'    => 'nullable|string',
            'paragraph'       => 'nullable|string',
            'multiple_choice' => 'nullable|array',
            'single_choice'   => 'nullable|string',
            'location'        => 'nullable|array',
            'file_upload'     => 'nullable|file|mimes:jpg,png,pdf|max:2048',
            'age'             => 'nullable|integer',
            'choice'          => 'nullable|string',
            'text_field'      => 'nullable|string',
            'rating'          => 'nullable|integer',
            'date_answer'     => 'nullable|date',
            'ranking'         => 'nullable|array',
            'likert'          => 'nullable|array',
            'nps'             => 'nullable|integer',
            'section'         => 'nullable|string',
        ]);

        // ✅ Handle file upload
        if ($request->hasFile('file_upload')) {
            $data['file_upload'] = $request->file('file_upload')->store('uploads', 'public');
        }

        // ✅ Manually JSON encode any arrays before insert
        foreach (['multiple_choice', 'location', 'ranking', 'likert'] as $jsonField) {
            if (array_key_exists($jsonField, $data)) {
                $data[$jsonField] = json_encode($data[$jsonField]);
            }
        }

        // ✅ Actually insert the response
        FormResponse::create($data);

        return back()->with('success', '✅ Response submitted successfully!');
    }

    // Show from Builder via encoded link
    public function respondViaUrl(Request $request)
    {
        $json = $request->query('data');
        $form = json_decode(urldecode($json), true);

        if (!empty($form['expires_at']) && now()->greaterThan($form['expires_at'])) {
            return view('form_expired');
        }

        return view('responses', [
            'form' => $form,
            'formJson' => $json,
        ]);
    }

    // Dashboard View
    public function dashboard()
    {
        $user = Auth::user();
        $forms = Form::where('user_id', $user->id)->latest()->get();
        $responses = FormResponse::whereIn('form_id', $forms->pluck('id'))->get()->groupBy('form_id');

        return view('dashboard', compact('user', 'forms', 'responses'));
    }

    // Admin: View Submissions
    public function viewResponses($formId)
    {
        $form = Form::findOrFail($formId);
        $responses = $form->responses;
        $formJson = json_encode($form);

        return view('admin.responses', compact('form', 'responses', 'formJson'));
    }

    // Delete a form
    public function deleteForm($id)
    {
        Form::findOrFail($id)->delete();

        return back()->with('success', '🗑️ Form deleted.');
    }
}
