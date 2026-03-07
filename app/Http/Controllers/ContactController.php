<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index()
    {
        $contact = Contact::first();
        if ($contact) {
            return redirect()->route('contacts.edit', $contact->id);
        }
        return view('contacts.index');
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

        $contact->update($data);

        return redirect()->route('contacts.index')->with('success', 'تم تحديث جهة الاتصال بنجاح');
    }

    public function destroy(Contact $contact)
    {
        $contact->delete();
        return redirect()->route('contacts.index')->with('success', 'تم حذف جهة الاتصال بنجاح');
    }
}
