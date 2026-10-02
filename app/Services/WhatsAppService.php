<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    private string $baseUrl;
    private string $token;

    public function __construct()
    {
        $instance = config('services.ultramsg.instance');
        $this->token = config('services.ultramsg.token');
        $this->baseUrl = "https://api.ultramsg.com/{$instance}";
    }

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

        try {
            $response = Http::withoutVerifying()->asForm()->post("{$this->baseUrl}/messages/chat", [
                'token' => $this->token,
                'to' => $phone,
                'body' => $message,
                'priority' => 1, // Low priority = أبطأ = أأمن
            ]);


            if ($response->json('sent') === 'true') {
                Log::info("WhatsApp Notification sent to $phone");
                return true;
            }

            Log::error("WhatsApp failed to $phone", $response->json());
            return false;

        } catch (\Exception $e) {
            Log::error("WhatsApp exception: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get instance status
     */
    /**
     * Send Image message
     */
    public function sendImage($phone, $imagePath, $caption = '')
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        try {
            $response = Http::withoutVerifying()->asForm()->post("{$this->baseUrl}/messages/image", [
                'token' => $this->token,
                'to' => $phone,
                'image' => $imagePath,
                'caption' => $caption,
            ]);
            return $response->json('sent') === 'true';
        } catch (\Exception $e) {
            Log::error("WhatsApp sendImage error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send Video message
     */
    public function sendVideo($phone, $videoPath, $caption = '')
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        try {
            $response = Http::withoutVerifying()->asForm()->post("{$this->baseUrl}/messages/video", [
                'token' => $this->token,
                'to' => $phone,
                'video' => $videoPath,
                'caption' => $caption,
            ]);
            return $response->json('sent') === 'true';
        } catch (\Exception $e) {
            Log::error("WhatsApp sendVideo error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send Document/File message
     */
    public function sendDocument($phone, $filePath, $fileName, $caption = '')
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        try {
            $response = Http::withoutVerifying()->asForm()->post("{$this->baseUrl}/messages/document", [
                'token' => $this->token,
                'to' => $phone,
                'document' => $filePath,
                'filename' => $fileName,
                'caption' => $caption,
            ]);
            return $response->json('sent') === 'true';
        } catch (\Exception $e) {
            Log::error("WhatsApp sendDocument error: " . $e->getMessage());
            return false;
        }
    }

    public function getStatus()
    {
        try {
            $response = Http::withoutVerifying()->get("{$this->baseUrl}/instance/status", [
                'token' => $this->token,
            ]);
            $data = $response->json();

            // Normalize status based on UltraMsg response structure
            if (isset($data['status']['accountStatus']['status'])) {
                $data['account_status'] = $data['status']['accountStatus']['status'];
            }

            return $data;

        } catch (\Exception $e) {
            Log::error("WhatsApp getStatus exception: " . $e->getMessage());
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }


    /**
     * Get QR Code
     */
    public function getQrCode()
    {
        return "{$this->baseUrl}/instance/qr?token={$this->token}";
    }

    /**
     * Logout / Disconnect
     */
    public function logout()
    {
        try {
            $response = Http::withoutVerifying()->asForm()->post("{$this->baseUrl}/instance/logout", [
                'token' => $this->token,
            ]);
            return $response->json();

        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }


    /**
     * Saudi Dialect Messages Templates
     */
    public static function getTemplate($type, $data = [])
    {
        $name = $data['name'] ?? '';
        $amount = $data['amount'] ?? '';
        $category = $data['category'] ?? '';
        $id = $data['id'] ?? '';
        
        $templates = [
            'new_expert_reg' => "يا مدير، فيه خبير جديد يبي ينضم لثمن! اسم الخبير: {$name}. شيك على بياناته باللوحة وخلص أموره يا بطل.",
            'new_withdrawal' => "الخبير {$name} يبي يسحب أرباحه ({$amount} ريال). تكفى لا تبطي عليه وراجع الطلب الحين.",
            'new_order_expert' => "يا خبيرنا، جاك رزق! فيه طلب تثمين جديد بقسمك [{$category}]. ادخل استلمه الحين قبل يطير عليك!\nللدخول السريع: " . url('/expert/login'),
            'order_accepted_other' => "معوض خير يا غالي، الطلب رقم {$id} استلمه خبير ثاني. خلك قريب للطلبات الجاية.",
            'order_accepted_expert' => "كفو يا وحش! استلمت الطلب رقم {$id}. تكفى نبي فزعتك تسرع بالتقييم عشان العميل ينتظرك.",
            'order_paid_customer' => "يا هلا والله! تم استلام مبلغك لطلبك رقم {$id}. طلبك الحين عند أفضل خبرائنا، خلك قريب بنبشرك قريب.",
            'order_evaluating_customer' => "بشرى سارة! خبيرنا المختص بدأ الحين يشتغل على طلبك رقم {$id}. شوي ويكون التقييم عندك.",
            'order_ready_customer' => "بشرنااااك! تقييم طلبك رقم {$id} صار جاهز الحين. تفضل شيك عليه بالمنصة وعطنا رايك.",
            'welcome_social' => "يا هلا والله بك يا {$name} في ثمن! نورتنا وشرفتنا، وأي خدمة حنا بالخدمة.",
            'withdrawal_approved' => "أبشر بالخير ! تمت الموافقة على طلب سحب أرباحك بمبلغ {$amount} ريال .. الحوالة في طريقها !",
            'withdrawal_rejected' => "عذراً .. تم رفض طلب سحب الأرباح الخاص بك و لمزيد من التفاصيل يرجى مراجعة لوحة التحكم أو التواصل مع الدعم",
            'expert_approved'  => "تستاهل ! تم تفعيل حسابك كخبير في تطبيق ثمن .. الحين تقدر تستلم طلبات التثمين وتبدأ رحلتك معنا !! للدخول السريع لحسابك : " . url('/expert/login'),
            'expert_order_done'=> "كفو يا وحش ! أكملت تثمين الطلب رقم {$id} بنجاح .. وصلت للعميل نتيجة التثمين الحين .. نتطلع لطلبات أكثر معاك",
            'expert_evaluation_warning' => "تنبيه يا الغالي .. مر أكثر من ساعة على استلامك الطلب رقم {$id} وما تم التقييم للحين ! رح قيم في أسرع وقت عشان ما ينسحب الطلب",
            'expert_order_withdrawn' => "للأسف .. تم سحب الطلب رقم {$id} منك لأنك تأخرت في التقييم وهذا لا يتناسب مع سياسة التطبيق ! الطلب رجع لباقي الخبراء نتطلع لتعاونك بشكل أسرع في الطلبات الجاية",
        ];

        return $templates[$type] ?? $type;
    }
}
