<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class AppLayout extends Component
{
    public $stats;
    public $recentOrders;
    public $recentPayments;

    public function __construct()
    {
        $this->stats = [
            'users_count' => \App\Models\User::count(),
            'experts_count' => \App\Models\User::role('expert')->count(),
            'orders_count' => \App\Models\Order::count(),
            'pending_orders' => \App\Models\Order::where('status', 'pending')->count(),
            'total_revenue' => \App\Models\TapPayment::where('status', 'CAPTURED')->sum('amount'), // Assuming CAPTURED is success
        ];

        $this->recentOrders = \App\Models\Order::with('user', 'category')->latest()->take(5)->get();
        $this->recentPayments = \App\Models\TapPayment::with('order.user')->latest()->take(5)->get();
    }

    /**
     * Get the view / contents that represents the component.
     */
    public function render(): View
    {
        return view('layouts.app');
    }
}
