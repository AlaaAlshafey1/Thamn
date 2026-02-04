<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index()
    {
        $contacts = Contact::latest()->paginate(10);
        return view('contacts.index', compact('contacts'));
    }

    public function create()
    {
        return view('contacts.form');
    }

public function store(Request $request)
{
    $request->validate([
        'phone' => 'nullable|string|max:255',
        'email' => 'nullable|email|max:255',
        // social_media optional
    ]);

    $data = $request->all();

    // تحويل social_media array إلى JSON
    if(isset($data['social_media']) && is_array($data['social_media'])) {
        $data['social_media'] = json_encode($data['social_media']);
    }

    Contact::create($data);

    return redirect()->route('contacts.index')->with('success', 'تمت إضافة جهة الاتصال بنجاح');
}



    public function edit(Contact $contact)
    {
        return view('contacts.form', compact('contact'));
    }

public function update(Request $request, Contact $contact)
{
    $request->validate([
        'phone' => 'nullable|string|max:255',
        'email' => 'nullable|email|max:255',
        // social_media optional
    ]);

    $data = $request->all();

    // تحويل social_media array إلى JSON
    if(isset($data['social_media']) && is_array($data['social_media'])) {
        $data['social_media'] = json_encode($data['social_media']);
    }

    $contact->update($data);

    return redirect()->route('contacts.index')->with('success', 'تم تحديث جهة الاتصال بنجاح');
}

    public function destroy(Contact $contact)
    {
        $contact->delete();
        return redirect()->route('contacts.index')->with('success', 'تم حذف جهة الاتصال بنجاح');
    }
}
