<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderDetails;
use App\Models\OrderFiles;
use App\Models\QuestionStep;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'status' => 'nullable|string',
            'answers' => 'required|array',
            'answers.*.question_id' => 'required|exists:questions,id',
            'answers.*.option_id' => 'nullable',
            'answers.*.sub_option_id' => 'nullable',
            'answers.*.value' => 'nullable|string',
            'answers.*.price' => 'nullable|numeric',
            'answers.*.status' => 'nullable|integer',
            'answers.*.stageing' => 'nullable|integer',
        ]);


        $order = Order::create([
            'user_id' => $user->id,
            'category_id' => $request->category_id ?? 1 ,
            'status' => $request->status ?? 0,
            'payload' => json_encode($request->answers),
        ]);

        $totalPrice = 0;
        $details = [];

        foreach ($request->answers as $answer) {
            $optionIds = is_array($answer['option_id'] ?? null) ? $answer['option_id'] : [$answer['option_id'] ?? null];

            foreach ($optionIds as $optionId) {
                if ($optionId === null) continue;

                $details[] = OrderDetails::create([
                    'order_id' => $order->id,
                    'question_id' => $answer['question_id'],
                    'option_id' => $optionId,
                    'sub_option_id' => $answer['sub_option_id'] ?? null,
                    'value' => $answer['value'] ?? null,
                    'price' => $answer['price'] ?? null,
                    'status' => $answer['status'] ?? 1,
                    'stageing' => $answer['stageing'] ?? null,
                ]);

                $totalPrice += $answer['price'] ?? 0;
            }
        }

        $order->update(['total_price' => $totalPrice]);
        $images = [];
        $files = [];

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $path = $file->store('orders/' . $order->id . '/images', 'public');
                $images[] = Storage::url($path);

                OrderFiles::create([
                    'order_id' => $order->id,
                    'file_name' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'type' => 'image',
                ]);
            }
        }

        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $path = $file->store('orders/' . $order->id . '/files', 'public');
                $files[] = Storage::url($path);

                OrderFiles::create([
                    'order_id' => $order->id,
                    'file_name' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'type' => 'file',
                ]);
            }
        }

        $responseAnswers = collect($details)->map(function($d) {
            return [
                'question_id' => $d->question_id,
                'option_id' => $d->option_id,
                'sub_option_id' => $d->sub_option_id,
                'value' => $d->value,
                'price' => $d->price,
                'status' => $d->status,
                'stageing' => $d->stageing,
            ];
        });

        return response()->json([
            'status' => true,
            'order' => [
                'id' => $order->id,
                'user_id' => $order->user_id,
                'status' => $order->status,
                'total_price' => $totalPrice,
                'files' => OrderFiles::where('order_id', $order->id)
                    ->where('type', 'file')
                    ->get()
                    ->map(function ($file) {
                        return [
                            'id'   => $file->id,
                            'name' => $file->file_name,
                            'url'  => full_url($file->file_path),
                        ];
                    }),

                'images' => OrderFiles::where('order_id', $order->id)
                    ->where('type', 'image')
                    ->get()
                    ->map(function ($file) {
                        return [
                            'id'   => $file->id,
                            'name' => $file->file_name,
                            'url'  => full_url($file->file_path),
                        ];
                    }),
                'answers' => $responseAnswers,
            ],
        ]);
    }

    public function allOrders()
    {
        $orders = Order::where("user_id",Auth::id())->with( 'category')
            ->latest()
            ->get();

        return response()->json(['status' => true, 'orders' => $orders]);
    }

    // ✅ أوردرات حسب حالة واحدة
    public function ordersByStatus($status)
    {
        $validStatuses = [
            'orderReceived','beingEstimated','beingReEstimated','estimated',
            'estimatedAndStored','reEstimated','inComplete','notPaid','cancelled'
        ];

        if (!in_array($status, $validStatuses)) {
            return response()->json(['status' => false, 'message' => 'Invalid status'], 400);
        }

        $orders = Order::where("user_id",Auth::id())->with( 'category')
            ->where('status', $status)
            ->latest()
            ->get();

        return response()->json(['status' => true, 'orders' => $orders]);
    }

    // ✅ أوردرات بحسب مجموعة predefined
    public function ordersByGroup($group)
    {
        $groups = [
            'inPricing' => ['beingEstimated','beingReEstimated'],
            'priced' => ['estimated','estimatedAndStored','reEstimated'],
            'incompleteOrCancelled' => ['inComplete','notPaid','cancelled']
        ];

        if (!array_key_exists($group, $groups)) {
            return response()->json(['status' => false, 'message' => 'Invalid group'], 400);
        }

        $orders = Order::where("user_id",Auth::id())->with('category')
            ->whereIn('status', $groups[$group])
            ->latest()
            ->get();

        return response()->json(['status' => true, 'orders' => $orders]);
    }

    // ✅ أوردرات بفلاتر متعددة: category_id, user_id, date range
    public function ordersFiltered(Request $request)
    {
        $query = Order::where("user_id",Auth::id())->with( 'category');

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }


        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $orders = $query->latest()->get();

        return response()->json(['status' => true, 'orders' => $orders]);
    }

}
