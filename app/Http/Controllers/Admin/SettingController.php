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
        return view('admin.settings.index', compact('settings'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'expert_commission_type' => 'required|in:fixed,percentage',
            'expert_commission_value' => 'required|numeric|min:0',
        ]);

        $oldType = Setting::where('key', 'expert_commission_type')->value('value');
        $oldValue = Setting::where('key', 'expert_commission_value')->value('value');

        Setting::updateOrCreate(['key' => 'expert_commission_type'], ['value' => $request->expert_commission_type]);
        Setting::updateOrCreate(['key' => 'expert_commission_value'], ['value' => $request->expert_commission_value]);

        // إذا تم تغيير النسبة، نقوم بإرسال رسالة للخبراء
        if ($oldType != $request->expert_commission_type || $oldValue != $request->expert_commission_value) {
            $this->notifyExperts($request->expert_commission_type, $request->expert_commission_value);
        }

        return redirect()->back()->with('success', 'تم حفظ الإعدادات بنجاح');
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
