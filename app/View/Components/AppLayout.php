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
        $user = auth()->user();

        if ($user && $user->hasAnyRole(['admin', 'superadmin'])) {
            $this->stats = [
                'users_count' => \App\Models\User::count(),
                'experts_count' => \App\Models\User::role('expert')->count(),
                'orders_count' => \App\Models\Order::count(),
                'pending_orders' => \App\Models\Order::where('status', 'pending')->count(),
                'total_revenue' => \App\Models\TapPayment::where('status', 'CAPTURED')->sum('amount'),
            ];

            $this->recentOrders = \App\Models\Order::with('user', 'category')->latest()->take(5)->get();
            $this->recentPayments = \App\Models\TapPayment::with('order.user')->latest()->take(5)->get();
        } elseif ($user && $user->hasRole('expert')) {
            $this->stats = [
                'users_count' => 0,
                'experts_count' => 0,
                'orders_count' => \App\Models\Order::where('expert_id', $user->id)->count(),
                'pending_orders' => \App\Models\Order::where('status', 'pending')
                    ->when($user->category_id, function ($q) use ($user) {
                        return $q->where(function ($sub) use ($user) {
                            $sub->where('category_id', $user->category_id)
                                ->orWhereNull('category_id');
                        });
                    })
                    ->whereDoesntHave('details', function ($q) {
                        $q->whereHas('question', function ($q2) {
                            $q2->where('type', 'rateTypeSelection');
                        })->where(function ($q3) {
                            $q3->whereHas('option', function ($q4) {
                                $q4->where('badge', 'ai');
                            })->orWhere('value', 'ai');
                        });
                    })->count(),
                'total_revenue' => 0, // Not applicable for expert in the same way
                'orders_completed' => \App\Models\Order::where('expert_id', $user->id)->where('status', 'estimated')->count(),
                'balance' => $user->balance ?? 0,
            ];

            $this->recentOrders = \App\Models\Order::where('expert_id', $user->id)->with('user', 'category')->latest()->take(5)->get();
            $this->recentPayments = collect();
        } else {
            $this->stats = [
                'users_count' => 0,
                'experts_count' => 0,
                'orders_count' => 0,
                'pending_orders' => 0,
                'total_revenue' => 0,
            ];
            $this->recentOrders = collect();
            $this->recentPayments = collect();
        }
    }

    /**
     * Get the view / contents that represents the component.
     */
    public function render(): View
    {
        return view('layouts.app');
    }
}
