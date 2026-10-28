<?php

namespace App\Http\Controllers;

use App\Models\Intro;
use Illuminate\Http\Request;

class IntroController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $intros = Intro::orderBy('sort_order')->get();
        return view('intros.index', compact('intros'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('intros.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'page' => 'required|string|max:100|unique:intros,page',
            'title_ar' => 'required|string|max:255',
            'title_en' => 'nullable|string|max:255',
            'sub_title_ar' => 'nullable|string|max:255',
            'sub_title_en' => 'nullable|string|max:255',
            'description_ar' => 'nullable|string',
            'description_en' => 'nullable|string',
            'sort_order' => 'nullable|integer',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $validated['title_en'] = $request->filled('title_en') ? $request->title_en : $request->title_ar;

        // Handle image upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . $request->page . '.' . $image->getClientOriginalExtension();
            $imagePath = $image->storeAs('intros', $imageName, 'public');
            $validated['image'] = asset('storage/' . $imagePath);
        }

        $validated['is_active'] = $request->has('is_active') ? (bool) $request->is_active : true;
        $validated['sort_order'] = $request->sort_order ?? 0;

        Intro::create($validated);

        return redirect()->route('intros.index')
            ->with('success', 'تم إنشاء صفحة المقدمة بنجاح.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Intro $intro)
    {
        return view('intros.show', compact('intro'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Intro $intro)
    {
        return view('intros.edit', compact('intro'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Intro $intro)
    {
        $validated = $request->validate([
            'page' => 'required|string|max:100|unique:intros,page,' . $intro->id,
            'title_ar' => 'required|string|max:255',
            'title_en' => 'nullable|string|max:255',
            'sub_title_ar' => 'nullable|string|max:255',
            'sub_title_en' => 'nullable|string|max:255',
            'description_ar' => 'nullable|string',
            'description_en' => 'nullable|string',
            'sort_order' => 'nullable|integer',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $validated['title_en'] = $request->filled('title_en') ? $request->title_en : $request->title_ar;

        // Handle image upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . $request->page . '.' . $image->getClientOriginalExtension();
            $imagePath = $image->storeAs('intros', $imageName, 'public');
            $validated['image'] = asset('storage/' . $imagePath);
        }

        $validated['is_active'] = $request->has('is_active') ? (bool) $request->is_active : false;
        $validated['sort_order'] = $request->sort_order ?? 0;

        $intro->update($validated);

        return redirect()->route('intros.index')
            ->with('success', 'تم تحديث صفحة المقدمة بنجاح.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Intro $intro)
    {
        $intro->delete();

        return redirect()->route('intros.index')
            ->with('success', 'تم حذف صفحة المقدمة بنجاح.');
    }
}
