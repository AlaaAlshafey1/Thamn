<?php

namespace App\Http\Controllers;

use App\Models\Color;
use Illuminate\Http\Request;

class ColorController extends Controller
{
    public function index()
    {
        $colors = Color::orderBy('group')->get();
        return view('colors.index', compact('colors'));
    }

    public function create()
    {
        return view('colors.form');
    }

    public function store(Request $request)
    {
        $request->validate([
            'group' => 'required|string',
            'key' => 'required|string|unique:colors,key',
            'value' => 'required|string',
        ]);

        Color::create($request->only('group','key','value'));

        return redirect()->route('colors.index')->with('success', 'تم إضافة اللون بنجاح');
    }

    public function edit(Color $color)
    {
        return view('colors.form', compact('color'));
    }

    public function update(Request $request, Color $color)
    {
        $request->validate([
            'group' => 'required|string',
            'key' => 'required|string|unique:colors,key,'.$color->id,
            'value' => 'required|string',
        ]);

        $color->update($request->only('group','key','value'));

        return redirect()->route('colors.index')->with('success', 'تم تحديث اللون بنجاح');
    }

    public function destroy(Color $color)
    {
        $color->delete();
        return redirect()->route('colors.index')->with('success', 'تم حذف اللون بنجاح');
    }
}
