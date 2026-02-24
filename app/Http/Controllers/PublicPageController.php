<?php

namespace App\Http\Controllers;

use App\Models\About;
use App\Models\TermCondition;
use Illuminate\Http\Request;

class PublicPageController extends Controller
{
    public function privacy(Request $request)
    {
        $page = About::where('type', 'privacy')->first();
        $title = lang('سياسة الخصوصية', 'Privacy Policy', $request);
        return view('public.page', compact('page', 'title'));
    }

    public function terms(Request $request)
    {
        $terms = About::where('type', 'terms')->first();
        $title = lang('الشروط والأحكام', 'Terms & Conditions', $request);
        return view('public.terms', compact('terms', 'title'));
    }

    public function about(Request $request)
    {
        $page = About::where('type', 'about')->first();
        $title = lang('عن ثمن', 'About Us', $request);
        return view('public.page', compact('page', 'title'));
    }
}
