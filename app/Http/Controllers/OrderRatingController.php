<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderRating;
use App\Models\User;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class OrderRatingController extends Controller
{
    /**
     * Show quick 1-click rating page for customer
     */
    public function show(Request $request, Order $order)
    {
        // Check signed URL validity
        if (!$request->hasValidSignature()) {
            abort(403, 'رابط التقييم غير صالح أو منتهي الصلاحية');
        }

        $order->load(['category', 'expert', 'rating']);
        $existingRating = $order->rating;

        return view('ratings.rate', compact('order', 'existingRating'));
    }

    /**
     * Submit rating from the quick 1-click web interface
     */
    public function submit(Request $request, Order $order)
    {
        if (!$request->hasValidSignature()) {
            return response()->json(['success' => false, 'message' => 'رابط غير صالح'], 403);
        }

        $validated = $request->validate([
            'rating'   => 'required|integer|between:1,5',
            'feedback' => 'nullable|string|max:1500',
        ]);

        $ratingValue = (int) $validated['rating'];
        $feedbackText = $validated['feedback'] ?? null;

        $rating = OrderRating::updateOrCreate(
            ['order_id' => $order->id],
            [
                'user_id'     => $order->user_id,
                'expert_id'   => $order->expert_id,
                'category_id' => $order->category_id,
                'rating'      => $ratingValue,
                'feedback'    => $feedbackText,
                'channel'     => 'quick_link',
            ]
        );

        // If customer rated negatively (1 or 2 stars), log alert and notify support
        if ($rating->isDissatisfied()) {
            Log::warning("⚠️ Low Customer Satisfaction Rating ({$ratingValue}/5) for Order #{$order->id} from Customer #{$order->user_id}");
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'شكراً لك! تم استلام تقييمك بنجاح',
                'rating'  => $rating,
            ]);
        }

        return back()->with('success', 'شكراً جزيلاً! تم تسجيل تقييمك بنجاح.');
    }

    /**
     * Handle inbound WhatsApp messages via UltraMsg Webhook
     */
    public function handleWhatsAppWebhook(Request $request)
    {
        $data = $request->all();
        Log::info('UltraMsg WhatsApp Webhook received:', $data);

        $eventType = $data['event_type'] ?? null;

        // We only care about incoming messages
        if ($eventType !== 'message_received') {
            return response()->json(['status' => 'ignored', 'reason' => 'not_message_received']);
        }

        $from = $data['data']['from'] ?? '';
        $body = trim((string) ($data['data']['body'] ?? ''));

        // Clean phone number (strip @c.us and non-digits)
        $phone = preg_replace('/[^0-9]/', '', explode('@', $from)[0]);

        if (empty($phone) || empty($body)) {
            return response()->json(['status' => 'ignored', 'reason' => 'empty_phone_or_body']);
        }

        // Parse rating number (supports 1-5, and Arabic numerals ١-٥)
        $rating = $this->parseRatingFromBody($body);
        if (!$rating) {
            return response()->json(['status' => 'ignored', 'reason' => 'not_a_rating_number']);
        }

        // Find customer by phone number (matches full or last 9 digits)
        $last9 = substr($phone, -9);
        $user = User::where(function ($q) use ($phone, $last9) {
            $q->where('phone', $phone)
                ->orWhere('phone', 'like', "%{$last9}")
                ->orWhere('phone', '0' . $last9);
        })->first();

        if (!$user) {
            return response()->json(['status' => 'ignored', 'reason' => 'user_not_found']);
        }

        // Find the latest evaluated order for this user (where survey was sent or status is estimated/evaluated)
        $order = Order::where('user_id', $user->id)
            ->where(function ($q) {
                $q->whereNotNull('survey_sent_at')
                    ->orWhereIn('status', ['estimated', 'evaluated', 'finished', 'completed', 'reEstimated']);
            })
            ->latest()
            ->first();

        if (!$order) {
            return response()->json(['status' => 'ignored', 'reason' => 'no_eligible_order_found']);
        }

        // Save or update the order rating
        $orderRating = OrderRating::updateOrCreate(
            ['order_id' => $order->id],
            [
                'user_id'     => $order->user_id,
                'expert_id'   => $order->expert_id,
                'category_id' => $order->category_id,
                'rating'      => $rating,
                'channel'     => 'whatsapp_chat',
            ]
        );

        // Send automatic reply back to the customer on WhatsApp
        try {
            $whatsapp = app(WhatsAppService::class);
            $customerName = $user->first_name ?: 'عميلنا العزيز';

            $template = match (true) {
                $rating >= 4 => 'survey_reply_positive',
                $rating == 3 => 'survey_reply_neutral',
                default      => 'survey_reply_negative',
            };

            $replyMsg = WhatsAppService::getTemplate($template, [
                'name' => $customerName,
                'id'   => $order->id,
            ]);

            $whatsapp->sendMessage($user->phone, $replyMsg);
        } catch (\Throwable $e) {
            Log::error("Failed to send automatic survey reply WhatsApp to {$user->phone}: " . $e->getMessage());
        }

        return response()->json([
            'status'   => 'success',
            'order_id' => $order->id,
            'rating'   => $rating,
        ]);
    }

    /**
     * Parse rating number 1-5 from message body
     */
    private function parseRatingFromBody(string $text): ?int
    {
        $arabicToEnglish = [
            '١' => 1, '٢' => 2, '٣' => 3, '٤' => 4, '٥' => 5,
            '1' => 1, '2' => 2, '3' => 3, '4' => 4, '5' => 5,
        ];

        $trimmed = trim($text);

        // Direct single digit
        if (isset($arabicToEnglish[$trimmed])) {
            return $arabicToEnglish[$trimmed];
        }

        // Extract first digit if user wrote "تقييمي 5" or "5 نجوم"
        if (preg_match('/[1-5١-٥]/u', $trimmed, $matches)) {
            $char = $matches[0];
            return $arabicToEnglish[$char] ?? null;
        }

        return null;
    }
}
