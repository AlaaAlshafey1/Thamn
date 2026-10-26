<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ObjectionReason;
use Illuminate\Http\Request;

class ObjectionReasonController extends Controller
{
    public function index()
    {
        return redirect()->route('admin.settings.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title_ar' => 'required|string|max:500',
            'title_en' => 'nullable|string|max:500',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        ObjectionReason::create([
            'title_ar' => $request->title_ar,
            'title_en' => $request->title_en,
            'sort_order' => $request->sort_order ?? 0,
            'is_active' => $request->has('is_active') ? (bool) $request->is_active : true,
        ]);

        return redirect()->back()->with('success', 'تمت إضافة سبب الاحتجاج بنجاح');
    }

    public function update(Request $request, $id)
    {
        $objectionReason = ObjectionReason::findOrFail($id);

        $request->validate([
            'title_ar' => 'required|string|max:500',
            'title_en' => 'nullable|string|max:500',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $objectionReason->update([
            'title_ar' => $request->title_ar,
            'title_en' => $request->title_en,
            'sort_order' => $request->sort_order ?? 0,
            'is_active' => $request->has('is_active') ? (bool) $request->is_active : false,
        ]);

        return redirect()->back()->with('success', 'تم تحديث سبب الاحتجاج بنجاح');
    }

    public function destroy($id)
    {
        $objectionReason = ObjectionReason::findOrFail($id);
        $objectionReason->delete();

        return redirect()->back()->with('success', 'تم حذف سبب الاحتجاج بنجاح');
    }

    public function toggle($id)
    {
        $objectionReason = ObjectionReason::findOrFail($id);
        $objectionReason->is_active = !$objectionReason->is_active;
        $objectionReason->save();

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'is_active' => $objectionReason->is_active,
                'message' => 'تم تغيير حالة السبب بنجاح',
            ]);
        }

        return redirect()->back()->with('success', 'تم تغيير حالة السبب بنجاح');
    }
}
