<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AiValuationRating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AiValuationRatingController extends Controller
{
    public function index()
    {
        $ratings = AiValuationRating::latest()->get();
        return view('admin.ai_valuation_ratings.index', compact('ratings'));
    }

    public function create()
    {
        return view('admin.ai_valuation_ratings.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name_ar' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'color' => 'nullable|string|max:50',
            'icon' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'is_active' => 'boolean'
        ]);

        $data = $request->except('icon');
        
        if ($request->hasFile('icon')) {
            $data['icon'] = $request->file('icon')->store('valuation_ratings', 'public');
        }

        AiValuationRating::create($data);

        return redirect()->route('admin.ai-valuation-ratings.index')->with('success', 'تم الإضافة بنجاح');
    }

    public function edit(AiValuationRating $aiValuationRating)
    {
        return view('admin.ai_valuation_ratings.edit', compact('aiValuationRating'));
    }

    public function update(Request $request, AiValuationRating $aiValuationRating)
    {
        $request->validate([
            'name_ar' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'color' => 'nullable|string|max:50',
            'icon' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'is_active' => 'boolean'
        ]);

        $data = $request->except('icon');
        $data['is_active'] = $request->has('is_active') ? $request->is_active : false;

        if ($request->hasFile('icon')) {
            if ($aiValuationRating->icon) {
                Storage::disk('public')->delete($aiValuationRating->icon);
            }
            $data['icon'] = $request->file('icon')->store('valuation_ratings', 'public');
        }

        $aiValuationRating->update($data);

        return redirect()->route('admin.ai-valuation-ratings.index')->with('success', 'تم التعديل بنجاح');
    }

    public function destroy(AiValuationRating $aiValuationRating)
    {
        if ($aiValuationRating->icon) {
            Storage::disk('public')->delete($aiValuationRating->icon);
        }
        $aiValuationRating->delete();
        
        return redirect()->route('admin.ai-valuation-ratings.index')->with('success', 'تم الحذف بنجاح');
    }
}
