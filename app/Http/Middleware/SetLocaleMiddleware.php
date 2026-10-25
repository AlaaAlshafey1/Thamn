<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocaleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $lang = 'ar'; // default
        
        if ($request->has('lang') && in_array($request->lang, ['ar', 'en'])) {
            $lang = $request->lang;
        } elseif ($request->hasSession() && $request->session()->has('locale') && in_array($request->session()->get('locale'), ['ar', 'en'])) {
            $lang = $request->session()->get('locale');
        } elseif ($request->header('Accept-Language') && strpos(strtolower($request->header('Accept-Language')), 'ar') !== false) {
            $lang = 'ar';
        }
        
        app()->setLocale($lang);
        // Also save it for web sessions if available
        if ($request->hasSession()) {
            $request->session()->put('locale', $lang);
        }

        return $next($request);
    }
}
