<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting;
use App\Models\User;
use App\Services\WhatsAppService;
use Illuminate\Support\Facades\Log;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::pluck('value', 'key')->toArray();
        $objectionReasons = \App\Models\ObjectionReason::orderBy('sort_order', 'asc')->orderBy('id', 'asc')->get();
        return view('admin.settings.index', compact('settings', 'objectionReasons'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'expert_commission_type' => 'required|in:fixed,percentage',
            'expert_commission_value' => 'required|numeric|min:0',
            'expert_agreement_pdf' => 'nullable|mimes:pdf',
        ]);

        $oldType = Setting::where('key', 'expert_commission_type')->value('value');
        $oldValue = Setting::where('key', 'expert_commission_value')->value('value');

        Setting::updateOrCreate(['key' => 'expert_commission_type'], ['value' => $request->expert_commission_type]);
        Setting::updateOrCreate(['key' => 'expert_commission_value'], ['value' => $request->expert_commission_value]);

        if ($request->hasFile('expert_agreement_pdf')) {
            $path = $request->file('expert_agreement_pdf')->store('settings', 'public');
            Setting::updateOrCreate(['key' => 'expert_agreement_pdf'], ['value' => $path]);
        }

        // إذا تم تغيير النسبة، نقوم بإرسال رسالة للخبراء
        if ($oldType != $request->expert_commission_type || $oldValue != $request->expert_commission_value) {
            $this->notifyExperts($request->expert_commission_type, $request->expert_commission_value);
        }

        return redirect()->back()->with('success', 'تم حفظ الإعدادات بنجاح');
    }

    /**
     * تحديث أرقام إصدارات تطبيقات الموبايل وروابط المتاجر وعناوين التحديث
     */
    public function updateAppVersions(Request $request)
    {
        $request->validate([
            'android_app_version'       => 'required|string|max:30',
            'ios_app_version'           => 'required|string|max:30',
            'android_store_url'         => 'nullable|string|max:500',
            'ios_store_url'             => 'nullable|string|max:500',
            'app_update_title_ar'       => 'nullable|string|max:255',
            'app_update_title_en'       => 'nullable|string|max:255',
            'app_update_description_ar' => 'nullable|string|max:500',
            'app_update_description_en' => 'nullable|string|max:500',
            'app_update_message_ar'     => 'nullable|string|max:500',
            'app_update_message_en'     => 'nullable|string|max:500',
            'android_force_update'      => 'nullable|in:0,1',
            'ios_force_update'          => 'nullable|in:0,1',
        ]);

        $titleAr = trim($request->app_update_title_ar ?? '');
        $titleEn = trim($request->app_update_title_en ?? '');
        $descAr = trim($request->app_update_description_ar ?: ($request->app_update_message_ar ?? ''));
        $descEn = trim($request->app_update_description_en ?: ($request->app_update_message_en ?? ''));

        Setting::updateOrCreate(['key' => 'android_app_version'], ['value' => trim($request->android_app_version)]);
        Setting::updateOrCreate(['key' => 'ios_app_version'], ['value' => trim($request->ios_app_version)]);
        Setting::updateOrCreate(['key' => 'android_store_url'], ['value' => trim($request->android_store_url ?? '')]);
        Setting::updateOrCreate(['key' => 'ios_store_url'], ['value' => trim($request->ios_store_url ?? '')]);
        Setting::updateOrCreate(['key' => 'app_update_title_ar'], ['value' => $titleAr]);
        Setting::updateOrCreate(['key' => 'app_update_title_en'], ['value' => $titleEn]);
        Setting::updateOrCreate(['key' => 'app_update_description_ar'], ['value' => $descAr]);
        Setting::updateOrCreate(['key' => 'app_update_description_en'], ['value' => $descEn]);
        // مزامنة مع المفتاح القديم للحفاظ على التوافق التام
        Setting::updateOrCreate(['key' => 'app_update_message_ar'], ['value' => $descAr]);
        Setting::updateOrCreate(['key' => 'app_update_message_en'], ['value' => $descEn]);
        Setting::updateOrCreate(['key' => 'android_force_update'], ['value' => $request->boolean('android_force_update') ? '1' : '0']);
        Setting::updateOrCreate(['key' => 'ios_force_update'], ['value' => $request->boolean('ios_force_update') ? '1' : '0']);

        return redirect()->back()->with('success', 'تم حفظ إعدادات إصدارات تطبيق الموبايل بنجاح');
    }

    private function notifyExperts($type, $value)
    {
        try {
            $whatsapp = app(WhatsAppService::class);
            $experts = User::role('expert')->get();

            $formattedValue = $type === 'percentage' ? "%{$value}" : "{$value} ريال";

            $message = "بشرى سارة لخبرائنا! 🎉\n";
            $message .= "تم تحديث نسبة أرباحكم لتصل إلى {$formattedValue} لكل طلب تقييم يتم إنجازه.\n";
            $message .= "شدوا الهمة ونتمنى لكم التوفيق والنجاح دائماً 🚀\n";
            $message .= "- فريق ثمن";

            foreach ($experts as $expert) {
                if ($expert->phone) {
                    $whatsapp->sendMessage($expert->phone, $message);
                }
            }
        } catch (\Exception $e) {
            Log::error('Failed to send WhatsApp notification to experts: ' . $e->getMessage());
        }
    }
}
