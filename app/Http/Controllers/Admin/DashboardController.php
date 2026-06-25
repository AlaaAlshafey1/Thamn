<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\User;

class DashboardController extends Controller
{
    public function askAI(Request $request)
    {
        $prompt = $request->input('prompt');
        $prompt = mb_strtolower($prompt);

        // Fetch some dynamic data for the "AI" to use
        $pendingOrders = Order::where('status', 'pending')->count();
        $expertsCount = User::role('expert')->count();
        $usersCount = User::count();

        // Very simple simulated AI logic based on keywords
        if (str_contains($prompt, 'طلبات') || str_contains($prompt, 'طلب') || str_contains($prompt, 'orders')) {
            $response = "يوجد حالياً $pendingOrders طلب بانتظار التقييم في النظام. أنصح بتنبيه الخبراء لإنهائها بسرعة للحفاظ على جودة الخدمة.";
        } elseif (str_contains($prompt, 'خبراء') || str_contains($prompt, 'خبير') || str_contains($prompt, 'experts')) {
            $response = "المنصة تمتلك $expertsCount خبير معتمد. يمكنك مراجعة نشاطهم من خلال قائمة 'الخبراء'.";
        } elseif (str_contains($prompt, 'مستخدم') || str_contains($prompt, 'عملاء') || str_contains($prompt, 'users')) {
            $response = "لدينا إجمالي $usersCount مستخدم مسجل. الأداء جيد ومعدل التسجيل في تزايد.";
        } elseif (str_contains($prompt, 'مشكلة') || str_contains($prompt, 'تأخير')) {
            $response = "لقد قمت بتحليل النظام ولا توجد أخطاء برمجية، ولكن يرجى متابعة الطلبات المعلقة ($pendingOrders طلب) لتجنب تأخير تقييم طلبات العملاء.";
        } elseif (str_contains($prompt, 'مرحبا') || str_contains($prompt, 'سلام')) {
            $response = "مرحباً بك! أنا مساعد ثمن الذكي، كيف يمكنني مساعدتك في إدارة المنصة اليوم؟";
        } else {
            $response = "قمت بتحليل استفسارك: '$prompt'. كنموذج ذكاء اصطناعي تجريبي لثمن، يمكنني حالياً تحليل بيانات الطلبات، المستخدمين، والخبراء. اسألني مثلاً عن 'الطلبات' أو 'الخبراء'.";
        }

        return response()->json([
            'response' => $response
        ]);
    }
}
