<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OrderRating;
use App\Models\User;
use Illuminate\Http\Request;

class RatingController extends Controller
{
    public function index(Request $request)
    {
        $query = OrderRating::with(['order', 'user', 'expert', 'category', 'resolver'])->latest();

        // Filter by sentiment / resolution
        $filter = $request->get('filter', 'all');
        if ($filter === 'unresolved') {
            $query->where('is_resolved', false)->whereIn('rating', [1, 2]);
        } elseif ($filter === 'negative') {
            $query->whereIn('rating', [1, 2]);
        } elseif ($filter === 'neutral') {
            $query->where('rating', 3);
        } elseif ($filter === 'positive') {
            $query->whereIn('rating', [4, 5]);
        }

        // Filter by Expert
        if ($request->filled('expert_id')) {
            $query->where('expert_id', $request->expert_id);
        }

        // Search by Order ID or User Phone/Name
        if ($request->filled('search')) {
            $term = trim($request->search);
            $query->where(function ($q) use ($term) {
                $q->where('order_id', $term)
                    ->orWhere('feedback', 'like', "%{$term}%")
                    ->orWhereHas('user', function ($sub) use ($term) {
                        $sub->where('first_name', 'like', "%{$term}%")
                            ->orWhere('last_name', 'like', "%{$term}%")
                            ->orWhere('phone', 'like', "%{$term}%");
                    });
            });
        }

        $ratings = $query->paginate(20)->withQueryString();

        // ─── KPI Calculations ───
        $totalRatings = OrderRating::count();
        $averageRating = $totalRatings > 0 ? round(OrderRating::avg('rating'), 1) : 0;
        $satisfiedCount = OrderRating::whereIn('rating', [4, 5])->count();
        $neutralCount = OrderRating::where('rating', 3)->count();
        $negativeCount = OrderRating::whereIn('rating', [1, 2])->count();
        $csatPercent = $totalRatings > 0 ? round(($satisfiedCount / $totalRatings) * 100) : 100;
        $unresolvedCount = OrderRating::where('is_resolved', false)->whereIn('rating', [1, 2])->count();

        // Distribution counts 5 to 1
        $distribution = [];
        for ($i = 5; $i >= 1; $i--) {
            $cnt = OrderRating::where('rating', $i)->count();
            $pct = $totalRatings > 0 ? round(($cnt / $totalRatings) * 100) : 0;
            $distribution[$i] = [
                'count'   => $cnt,
                'percent' => $pct,
            ];
        }

        // Expert Performance Breakdown
        $expertsStats = OrderRating::whereNotNull('expert_id')
            ->selectRaw('expert_id, COUNT(*) as total_reviews, AVG(rating) as avg_rating')
            ->groupBy('expert_id')
            ->with('expert')
            ->orderByDesc('avg_rating')
            ->get();

        return view('admin.ratings.index', compact(
            'ratings',
            'totalRatings',
            'averageRating',
            'satisfiedCount',
            'neutralCount',
            'negativeCount',
            'csatPercent',
            'unresolvedCount',
            'distribution',
            'expertsStats',
            'filter'
        ));
    }

    /**
     * Mark low rating / dissatisfaction as resolved by CS
     */
    public function resolve(Request $request, OrderRating $rating)
    {
        $request->validate([
            'admin_notes' => 'required|string|max:2000',
        ]);

        $rating->update([
            'is_resolved' => true,
            'admin_notes' => $request->admin_notes,
            'resolved_at' => now(),
            'resolved_by' => auth()->id(),
        ]);

        return back()->with('success', 'تم تسجيل متابعة وحل إشكالية العميل بنجاح.');
    }
}
