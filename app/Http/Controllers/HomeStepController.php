<?php

namespace App\Http\Controllers;

use App\Models\HomeStep;
use Illuminate\Http\Request;

class HomeStepController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $homeSteps = HomeStep::orderBy('sort_order')->get();
        return view('home_steps.index', compact('homeSteps'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('home_steps.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title_ar' => 'required|string|max:255',
            'title_en' => 'required|string|max:255',
            'sub_title_ar' => 'nullable|string|max:255',
            'sub_title_en' => 'nullable|string|max:255',
            'desc_ar' => 'nullable|string',
            'desc_en' => 'nullable|string',
            'type' => 'required|in:steps,check,image',
            'items' => 'required|array',
            'items.*.label' => 'required|string',
            'items.*.value' => 'nullable|string',
            'items.*.image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        // Process items and handle image uploads
        $items = [];
        foreach ($request->items as $index => $item) {
            $imagePath = null;

            // Handle image upload if type is 'image' and file exists
            if ($request->type === 'image' && $request->hasFile("items.{$index}.image")) {
                $image = $request->file("items.{$index}.image");
                $imageName = time() . '_' . $index . '.' . $image->getClientOriginalExtension();
                $imagePath = $image->storeAs('home_steps', $imageName, 'public');
                $imagePath = asset('storage/' . $imagePath);
            }

            $items[] = [
                'label' => $item['label'] ?? '',
                'value' => $item['value'] ?? '',
                'image' => $imagePath,
            ];
        }

        $validated['items'] = $items;
        $validated['is_active'] = $request->has('is_active');

        HomeStep::create($validated);

        return redirect()->route('home_steps.index')
            ->with('success', 'Home step created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(HomeStep $homeStep)
    {
        return view('home_steps.show', compact('homeStep'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(HomeStep $homeStep)
    {
        return view('home_steps.edit', compact('homeStep'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, HomeStep $homeStep)
    {
        $validated = $request->validate([
            'title_ar' => 'required|string|max:255',
            'title_en' => 'required|string|max:255',
            'sub_title_ar' => 'nullable|string|max:255',
            'sub_title_en' => 'nullable|string|max:255',
            'desc_ar' => 'nullable|string',
            'desc_en' => 'nullable|string',
            'type' => 'required|in:steps,check,image',
            'items' => 'required|array',
            'items.*.label' => 'required|string',
            'items.*.value' => 'required|string',
            'items.*.image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',

        ]);

        // Process items and handle image uploads
        $items = [];
        $existingItems = $homeStep->items ?? [];

        foreach ($request->items as $index => $item) {
            $imagePath = $existingItems[$index]['image'] ?? null; // Keep existing image

            // Handle new image upload if type is 'image' and file exists
            if ($request->type === 'image' && $request->hasFile("items.{$index}.image")) {
                $image = $request->file("items.{$index}.image");
                $imageName = time() . '_' . $index . '.' . $image->getClientOriginalExtension();
                $imagePath = $image->storeAs('home_steps', $imageName, 'public');
                $imagePath = asset('storage/' . $imagePath);
            }

            $items[] = [
                'label' => $item['label'] ?? '',
                'value' => $item['value'] ?? '',
                'image' => $imagePath,
            ];
        }

        $validated['items'] = $items;
        $validated['is_active'] = $request->has('is_active');

        $homeStep->update($validated);

        return redirect()->route('home_steps.index')
            ->with('success', 'Home step updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(HomeStep $homeStep)
    {
        $homeStep->delete();

        return redirect()->route('home_steps.index')
            ->with('success', 'Home step deleted successfully.');
    }
}
