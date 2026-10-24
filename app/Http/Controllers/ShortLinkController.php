<?php

namespace App\Http\Controllers;

use App\Models\ShortLink;
use Illuminate\Http\Request;

class ShortLinkController extends Controller
{
    public function redirect(Request $request, string $code)
    {
        $link = ShortLink::where('code', $code)->first();

        if (!$link || ($link->expires_at && $link->expires_at->isPast())) {
            return redirect()->route('expert.login')
                ->withErrors(['identifier' => 'الرابط غير صالح أو منتهي الصلاحية']);
        }

        // WhatsApp / social crawlers: return only a preview card (title + logo)
        // and never follow the target, so auto-login links are not triggered by bots.
        $ua = strtolower((string) $request->userAgent());
        foreach (['whatsapp', 'facebookexternalhit', 'twitterbot', 'telegrambot', 'slackbot', 'linkedinbot'] as $bot) {
            if (str_contains($ua, $bot)) {
                $title = e($link->title ?: 'منصة ثمن - اعتماد التثمين');
                $image = e('https://thmmn.net/assets/emails/logo_white.png');
                return response(
                    '<!DOCTYPE html><html lang="ar" dir="rtl"><head><meta charset="utf-8">'
                    . "<title>{$title}</title>"
                    . "<meta property=\"og:title\" content=\"{$title}\">"
                    . '<meta property="og:description" content="اضغط هنا لفتح الرابط في المتصفح">'
                    . "<meta property=\"og:image\" content=\"{$image}\">"
                    . '<meta property="og:type" content="website">'
                    . '</head><body></body></html>',
                    200
                )->header('Content-Type', 'text/html; charset=UTF-8');
            }
        }

        $link->increment('hits');

        return redirect()->away($link->url);
    }
}
