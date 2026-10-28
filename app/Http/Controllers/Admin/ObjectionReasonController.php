<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ObjectionReason;
use App\Models\Setting;
use Illuminate\Http\Request;

class ObjectionReasonController extends Controller
{
    public function index()
    {
        return redirect()->route('admin.settings.index');
    }

    public function updateFormTitle(Request $request)
    {
        $request->validate([
            'objection_form_title_ar' => 'required|string|max:500',
            'objection_form_title_en' => 'nullable|string|max:500',
        ]);

        Setting::updateOrCreate(
            ['key' => 'objection_form_title_ar'],
            ['value' => $request->objection_form_title_ar]
        );

        Setting::updateOrCreate(
            ['key' => 'objection_form_title_en'],
            ['value' => $request->objection_form_title_en ?: 'Select the reason for your dissatisfaction with the valuation']
        );

        return redirect()->back()->with('success', 'تم تحديث عنوان نافذة الاحتجاج بنجاح');
    }

    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:cat_with_sub_cat,input_modification,other_reason',
            'title_ar' => 'required|string|max:500',
            'title_en' => 'nullable|string|max:500',
            'subtitle_ar' => 'nullable|string|max:500',
            'subtitle_en' => 'nullable|string|max:500',
            'icon' => 'nullable|string|max:100',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
            'option_titles_ar' => 'nullable|array',
            'option_titles_en' => 'nullable|array',
            'option_ids' => 'nullable|array',
        ]);

        $defaultIcon = match ($request->type) {
            'cat_with_sub_cat' => 'price_estimation',
            'input_modification' => 'input_modification',
            default => 'other_reason',
        };

        $reason = ObjectionReason::create([
            'type' => $request->type,
            'title_ar' => $request->title_ar,
            'title_en' => $request->title_en,
            'subtitle_ar' => $request->subtitle_ar,
            'subtitle_en' => $request->subtitle_en,
            'icon' => $request->icon ?: $defaultIcon,
            'options' => null,
            'sort_order' => $request->sort_order ?? 0,
            'is_active' => $request->has('is_active') ? (bool) $request->is_active : true,
        ]);

        if ($request->type === 'cat_with_sub_cat') {
            $options = $this->buildOptionsArray($request, $reason->id);
            $reason->update(['options' => $options]);
        }

        return redirect()->back()->with('success', 'تمت إضافة سبب الاحتجاج بنجاح');
    }

    public function update(Request $request, $id)
    {
        $objectionReason = ObjectionReason::findOrFail($id);

        $request->validate([
            'type' => 'required|in:cat_with_sub_cat,input_modification,other_reason',
            'title_ar' => 'required|string|max:500',
            'title_en' => 'nullable|string|max:500',
            'subtitle_ar' => 'nullable|string|max:500',
            'subtitle_en' => 'nullable|string|max:500',
            'icon' => 'nullable|string|max:100',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
            'option_titles_ar' => 'nullable|array',
            'option_titles_en' => 'nullable|array',
            'option_ids' => 'nullable|array',
        ]);

        $defaultIcon = match ($request->type) {
            'cat_with_sub_cat' => 'price_estimation',
            'input_modification' => 'input_modification',
            default => 'other_reason',
        };

        $options = null;
        if ($request->type === 'cat_with_sub_cat') {
            $options = $this->buildOptionsArray($request, $objectionReason->id);
        }

        $objectionReason->update([
            'type' => $request->type,
            'title_ar' => $request->title_ar,
            'title_en' => $request->title_en,
            'subtitle_ar' => $request->subtitle_ar,
            'subtitle_en' => $request->subtitle_en,
            'icon' => $request->icon ?: $defaultIcon,
            'options' => $options,
            'sort_order' => $request->sort_order ?? 0,
            'is_active' => $request->has('is_active') ? (bool) $request->is_active : false,
        ]);

        return redirect()->back()->with('success', 'تم تحديث سبب الاحتجاج بنجاح');
    }

    private function buildOptionsArray(Request $request, int $reasonId): array
    {
        $titlesAr = $request->input('option_titles_ar', []);
        $titlesEn = $request->input('option_titles_en', []);
        $ids = $request->input('option_ids', []);

        $options = [];
        $baseId = $reasonId * 1000;
        $usedIds = [];

        foreach ($titlesAr as $index => $titleAr) {
            $titleAr = trim((string) $titleAr);
            $titleEn = trim((string) ($titlesEn[$index] ?? ''));

            if ($titleAr === '' && $titleEn === '') {
                continue;
            }

            $optId = isset($ids[$index]) && is_numeric($ids[$index]) && (int) $ids[$index] > 0
                ? (int) $ids[$index]
                : ($baseId + count($options) + 1);

            while (in_array($optId, $usedIds, true)) {
                $optId++;
            }
            $usedIds[] = $optId;

            $options[] = [
                'id' => $optId,
                'title_ar' => $titleAr ?: $titleEn,
                'title_en' => $titleEn ?: $titleAr,
            ];
        }

        return $options;
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
