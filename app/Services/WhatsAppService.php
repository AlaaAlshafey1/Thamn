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
        $this->token   = config('services.ultramsg.token');
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
                'token'    => $this->token,
                'to'       => $phone,
                'body'     => $message,
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
                'token'   => $this->token,
                'to'      => $phone,
                'image'   => $imagePath,
                'caption' => $caption,
            ]);
            return $response->json('sent') === 'true';
        } catch (\Exception $e) {
            Log::error("WhatsApp sendImage error: " . $e->getMessage());
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
                'token'    => $this->token,
                'to'       => $phone,
                'document' => $filePath,
                'filename' => $fileName,
                'caption'  => $caption,
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
        $templates = [
            'new_expert_reg' => "يا مدير، فيه خبير جديد يبي ينضم لثمن! اسم الخبير: {$data['name']}. شيك على بياناته باللوحة وخلص أموره يا بطل.",
            'new_withdrawal' => "الخبير {$data['name']} يبي يسحب أرباحه ({$data['amount']} ريال). تكفى لا تبطي عليه وراجع الطلب الحين.",
            'new_order_expert' => "يا خبيرنا، جاك رزق! فيه طلب تثمين جديد بقسمك [{$data['category']}]. ادخل استلمه الحين قبل يطير عليك!",
            'order_accepted_other' => "معوض خير يا غالي، الطلب رقم {$data['id']} استلمه خبير ثاني. خلك قريب للطلبات الجاية.",
            'order_accepted_expert' => "كفو يا وحش! استلمت الطلب رقم {$data['id']}. تكفى نبي فزعتك تسرع بالتقييم عشان العميل ينتظرك.",
            'order_paid_customer' => "يا هلا والله! تم استلام مبلغك لطلبك رقم {$data['id']}. طلبك الحين عند أفضل خبرائنا، خلك قريب بنبشرك قريب.",
            'order_evaluating_customer' => "بشرى سارة! خبيرنا المختص بدأ الحين يشتغل على طلبك رقم {$data['id']}. شوي ويكون التقييم عندك.",
            'order_ready_customer' => "بشرنااااك! تقييم طلبك رقم {$data['id']} صار جاهز الحين. تفضل شيك عليه بالمنصة وعطنا رايك.",
            'welcome_social' => "يا هلا والله بك يا {$data['name']} في ثمن! نورتنا وشرفتنا، وأي خدمة حنا بالخدمة.",
            'withdrawal_approved' => "بشرى سارة! تم الموافقة على طلب سحب أرباحك بمبلغ {$data['amount']} ريال. الحوالة في طريقها لك يا بطل.",
            'withdrawal_rejected' => "عذراً يا خبيرنا، تم رفض طلب سحب الأرباح الخاص بك. لمزيد من التفاصيل يرجى مراجعة لوحة التحكم أو التواصل مع الدعم.",
            'expert_approved' => "مبروك! تم تفعيل حسابك كخبير في منصة ثمن. الحين تقدر تستلم طلبات التثمين وتبدأ رحلتك معنا. نورتنا يا وحش!",


        ];
 
        return $templates[$type] ?? $type;
    }
}
