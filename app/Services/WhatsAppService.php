<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    /**
     * Send a WhatsApp message
     * 
     * @param string $phone Phone number with country code (e.g., 9665xxxxxxxx)
     * @param string $message The message in Saudi Colloquial
     * @return bool
     */
    public function sendMessage($phone, $message)
    {
        // Sanitize phone: remove +, spaces, and leading 00
        $phone = preg_replace('/[^0-9]/', '', $phone);
        if (str_starts_with($phone, '05')) {
            $phone = '966' . substr($phone, 1);
        }

        Log::info("WhatsApp Notification sent to $phone: $message");

        // Here you would integrate with your WhatsApp provider API
        // Example for Ultramsg:
        /*
        $params = [
            'token' => config('services.whatsapp.token'),
            'to' => $phone,
            'body' => $message
        ];
        Http::post("https://api.ultramsg.com/" . config('services.whatsapp.instance') . "/messages/chat", $params);
        */

        return true;
    }

    /**
     * Saudi Dialect Messages Templates
     */
    public static function getTemplate($type, $data = [])
    {
        $templates = [
            'new_expert_reg' => "يا مدير، فيه خبير جديد يبي ينضم لثمن! اسم الخبير: {$data['name']}. شيك على بياناته باللوحة وخلص أموره يا بطل.",
            'new_withdrawal' => "الخبير {$data['name']} يبي يسحب أرباحه ({$data['amount']} ريال). تكفى لا تبطي عليه وراجع الطلب الحين.",
            'new_order_expert' => "يا خبيرنا، جاك رزق! فيه طلب تثمين جديد بقسمك [{$data['category']}]. ادخل استلمه الحين قبل يطير عليك!",
            'order_accepted_other' => "معوض خير يا غالي، الطلب رقم {$data['id']} استلمه خبير ثاني. خلك قريب للطلبات الجاية.",
            'order_accepted_expert' => "كفو يا وحش! استلمت الطلب رقم {$data['id']}. تكفى نبي فزعتك تسرع بالتقييم عشان العميل ينتظرك.",
            'order_paid_customer' => "يا هلا والله! تم استلام مبلغك لطلبك رقم {$data['id']}. طلبك الحين عند أفضل خبرائنا، خلك قريب بنبشرك قريب.",
            'order_evaluating_customer' => "بشرى سارة! خبيرنا المختص بدأ الحين يشتغل على طلبك رقم {$data['id']}. شوي ويكون التقييم عندك.",
            'order_ready_customer' => "بشرنااااك! تقييم طلبك رقم {$data['id']} صار جاهز الحين. تفضل شيك عليه بالمنصة وعطنا رايك.",
        ];

        return $templates[$type] ?? $type;
    }
}
