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
            'page' => 'required|in:welcome,login,signup,verify|unique:intros,page',
            'title_ar' => 'required|string|max:255',
            'title_en' => 'required|string|max:255',
            'sub_title_ar' => 'nullable|string|max:255',
            'sub_title_en' => 'nullable|string|max:255',
            'description_ar' => 'nullable|string',
            'description_en' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);
        // Handle image upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . $request->page . '.' . $image->getClientOriginalExtension();
            $imagePath = $image->storeAs('intros', $imageName, 'public');
            $validated['image'] = asset('storage/' . $imagePath);
        }

        $validated['is_active'] = $request->has('is_active');

        Intro::create($validated);

        return redirect()->route('intros.index')
            ->with('success', 'Intro page created successfully.');
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
            'page' => 'required|in:welcome,login,signup,verify|unique:intros,page,' . $intro->id,
            'title_ar' => 'required|string|max:255',
            'title_en' => 'required|string|max:255',
            'sub_title_ar' => 'nullable|string|max:255',
            'sub_title_en' => 'nullable|string|max:255',
            'description_ar' => 'nullable|string',
            'description_en' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . $request->page . '.' . $image->getClientOriginalExtension();
            $imagePath = $image->storeAs('intros', $imageName, 'public');
            $validated['image'] = asset('storage/' . $imagePath);
        }

        $validated['is_active'] = $request->has('is_active');

        $intro->update($validated);

        return redirect()->route('intros.index')
            ->with('success', 'Intro page updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Intro $intro)
    {
        $intro->delete();

        return redirect()->route('intros.index')
            ->with('success', 'Intro page deleted successfully.');
    }
}
