<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Reservation;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with(['reservation.room', 'customer', 'waiter'])
            ->orderBy('order_time', 'desc')
            ->paginate(20);

        return view('orders.index', compact('orders'));
    }

    public function create(Request $request)
    {
        $reservation = null;
        if ($request->reservation_id) {
            $reservation = Reservation::with(['room', 'customer'])->find($request->reservation_id);
        }

        $categories = Category::where('is_active', true)
            ->with(['products' => function ($q) {
                $q->where('is_available', true);
            }])
            ->orderBy('sort_order')
            ->get();

        $popularProducts = Product::where('is_popular', true)
            ->where('is_available', true)
            ->get();

        return view('orders.create', compact('reservation', 'categories', 'popularProducts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'reservation_id' => 'required|exists:reservations,id',
            'products' => 'required|array',
            'products.*.id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
        ]);

        $reservation = Reservation::find($request->reservation_id);

        $order = Order::create([
            'reservation_id' => $reservation->id,
            'customer_id' => $reservation->customer_id,
            'waiter_id' => auth()->id(),
            'subtotal' => 0,
            'total_amount' => 0,
            'discount_amount' => $request->discount_amount ?? 0,
            'notes' => $request->notes,
        ]);

        // Add order items
        foreach ($request->products as $productData) {
            $product = Product::find($productData['id']);
            $totalPrice = $product->price * $productData['quantity'];

            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'quantity' => $productData['quantity'],
                'unit_price' => $product->price,
                'total_price' => $totalPrice,
                'special_instructions' => $productData['instructions'] ?? null,
            ]);
        }

        // Calculate totals
        $order->calculateTotal();

        return redirect()->route('orders.show', $order)
            ->with('success', 'Buyurtma muvaffaqiyatli yaratildi!');
    }

    public function show(Order $order)
    {
        $order->load(['reservation.room', 'customer', 'waiter', 'items.product.category']);

        return view('orders.show', compact('order'));
    }
    public function edit(Order $order)
    {
        return view('orders.edit', compact('order'));
    }
    public function update(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,preparing,ready,served,completed',
            'discount_amount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'item_status' => 'nullable|array',
            'item_status.*' => 'in:pending,preparing,ready,served'
        ]);

        $order->update($request->only(['status', 'discount_amount', 'notes']));

        // Update order items status
        if ($request->item_status) {
            foreach ($request->item_status as $itemId => $status) {
                $order->items()->where('id', $itemId)->update(['status' => $status]);
            }
        }

        // Recalculate total
        $order->calculateTotal();

        return redirect()->route('orders.show', $order)
            ->with('success', 'Buyurtma muvaffaqiyatli yangilandi!');
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,preparing,ready,served,completed'
        ]);

        $order->update(['status' => $request->status]);

        if ($request->status === 'served') {
            $order->update(['served_time' => now()]);
        }

        return back()->with('success', 'Buyurtma holati yangilandi!');
    }

    // Kitchen display for orders
    public function kitchen()
    {
        $orders = Order::with(['reservation.room', 'items.product'])
            ->whereIn('status', ['pending', 'preparing'])
            ->orderBy('order_time')
            ->get();

        return view('orders.kitchen', compact('orders'));
    }
    public function getOrdersByStatus($status)
    {
        $orders = Order::where('status', $status)
            ->with(['reservation.room', 'customer', 'items.product'])
            ->orderBy('order_time', 'desc')
            ->get();

        return response()->json([
            'orders' => $orders,
            'count' => $orders->count()
        ]);
    }
    // public function update(Request $request, Order $order)
    // {
    //     $request->validate([
    //         'notes' => 'nullable|string',
    //         'status' => 'nullable|in:pending,preparing,ready,served,completed'
    //     ]);

    //     $order->update($request->only(['notes', 'status']));

    //     return response()->json(['success' => true]);
    // }
}
