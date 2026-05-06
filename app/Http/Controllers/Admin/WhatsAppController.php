<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;

class WhatsAppController extends Controller
{
    protected $whatsapp;

    public function __construct(WhatsAppService $whatsapp)
    {
        $this->whatsapp = $whatsapp;
    }

    public function index()
    {
        $status = $this->whatsapp->getStatus();
        $qrCode = $this->whatsapp->getQrCode();

        return view('admin.whatsapp.index', compact('status', 'qrCode'));
    }

    public function logout()
    {
        $response = $this->whatsapp->logout();
        
        if (isset($response['status']) && $response['status'] == 'success') {
            return redirect()->back()->with('success', 'تم تسجيل الخروج من واتساب بنجاح');
        }

        return redirect()->back()->with('error', 'فشل تسجيل الخروج');
    }
}
