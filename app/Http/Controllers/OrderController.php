<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\Category;
use App\Models\Customer;
use App\Models\OrderItem;
use App\Models\Reservation;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with(['reservation.room', 'customer', 'waiter'])
            ->orderBy('order_time', 'desc')
            ->paginate(20);

        $stats = [
            'dine_in' => Order::where('order_type', 'dine_in')->whereDate('order_time', today())->count(),
            'takeaway' => Order::where('order_type', 'takeaway')->whereDate('order_time', today())->count(),
            'delivery' => Order::where('order_type', 'delivery')->whereDate('order_time', today())->count(),
            'total_today' => Order::whereDate('order_time', today())->count(),
        ];

        return view('orders.index', compact('orders', 'stats'));
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
    public function returnedItems()
    {
        $returnedItems = OrderItem::where('status', 'returned')
            ->with(['order.customer', 'order.reservation.room', 'product'])
            ->orderBy('updated_at', 'desc')
            ->paginate(20);

        return view('orders.returned', compact('returnedItems'));
    }
    public function edit(Order $order)
    {
        $order->load(['items.product.category', 'reservation.room', 'customer', 'waiter']);
        $categories = Category::with(['products' => function ($query) {
            $query->where('is_available', true);
        }])->where('is_active', true)->orderBy('sort_order')->get();

        return view('orders.edit', compact('order', 'categories'));
    }
    public function update(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,preparing,ready,served,completed',
            'discount_amount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'items' => 'nullable|array',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.status' => 'required|in:pending,preparing,ready,served,returned',
            'items.*.special_instructions' => 'nullable|string',
            'items.*.remove' => 'nullable|boolean',
            'new_items' => 'nullable|array',
            'new_items.*.product_id' => 'required|exists:products,id',
            'new_items.*.quantity' => 'required|integer|min:1',
            'new_items.*.unit_price' => 'required|numeric|min:0',
            'new_items.*.status' => 'required|in:pending,preparing,ready',
            'new_items.*.special_instructions' => 'nullable|string',
        ]);

        \DB::transaction(function () use ($request, $order) {
            // Update order basic info
            $order->update([
                'status' => $request->status,
                'discount_amount' => $request->discount_amount ?? 0,
                'notes' => $request->notes,
            ]);

            // Handle existing items
            if ($request->items) {
                foreach ($request->items as $itemId => $itemData) {
                    $orderItem = $order->items()->find($itemId);

                    if ($orderItem) {
                        if (isset($itemData['remove']) && $itemData['remove']) {
                            // Remove item (soft delete or hard delete based on business logic)
                            if ($itemData['status'] === 'served') {
                                // If already served, mark as returned instead of deleting
                                $orderItem->update([
                                    'status' => 'returned',
                                    'special_instructions' => 'Qaytarildi: ' . ($itemData['special_instructions'] ?? '')
                                ]);
                            } else {
                                // If not served yet, can safely delete
                                $orderItem->delete();
                            }
                        } else {
                            // Update existing item
                            $totalPrice = $orderItem->unit_price * $itemData['quantity'];
                            $orderItem->update([
                                'quantity' => $itemData['quantity'],
                                'total_price' => $totalPrice,
                                'status' => $itemData['status'],
                                'special_instructions' => $itemData['special_instructions'],
                            ]);
                        }
                    }
                }
            }

            // Handle new items
            if ($request->new_items) {
                foreach ($request->new_items as $newItemData) {
                    $totalPrice = $newItemData['unit_price'] * $newItemData['quantity'];

                    $order->items()->create([
                        'product_id' => $newItemData['product_id'],
                        'quantity' => $newItemData['quantity'],
                        'unit_price' => $newItemData['unit_price'],
                        'total_price' => $totalPrice,
                        'status' => $newItemData['status'],
                        'special_instructions' => $newItemData['special_instructions'],
                    ]);
                }
            }

            // Recalculate order totals
            $order->calculateTotal();
        });

        // Handle different submit actions
        if ($request->action === 'save_and_print') {
            return redirect()->route('orders.show', $order)
                ->with('success', 'Buyurtma yangilandi!')
                ->with('print', true);
        }

        return redirect()->route('orders.show', $order)
            ->with('success', 'Buyurtma muvaffaqiyatli yangilandi!');
    }

    public function updateStatus(Request $request, Order $order)
    {
        try {
            // Validatsiya
            $request->validate([
                'status' => 'required|in:pending,preparing,ready,served,completed'
            ]);

            // Status va vaqtni bir vaqtning o'zida yangilash
            $updateData = ['status' => $request->status];

            // Agar "completed" bo'lsa, tugallanish vaqtini qo'shish
            if ($request->status === 'completed') {
                $updateData['completed_time'] = now();
            }

            $order->update($updateData);

            // AJAX uchun JSON javob
            return response()->json([
                'success' => true,
                'message' => 'Buyurtma holati muvaffaqiyatli o\'zgartirildi',
                'order' => $order->fresh(), // Yangilangan ma'lumot
                'status' => $request->status
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validatsiya xatosi',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Server xatosi: ' . $e->getMessage()
            ], 500);
        }
    }

    // Kitchen display for orders
    public function kitchen()
    {
        $orders = Order::with(['reservation.room', 'items.product', 'customer'])
            ->whereIn('status', ['pending', 'preparing'])
            ->orderBy('order_time')
            ->get();

        $stats = [
            'dine_in_pending' => $orders->where('order_type', 'dine_in')->where('status', 'pending')->count(),
            'takeaway_pending' => $orders->where('order_type', 'takeaway')->where('status', 'pending')->count(),
            'delivery_pending' => $orders->where('order_type', 'delivery')->where('status', 'pending')->count(),
        ];

        return view('orders.kitchen', compact('orders', 'stats'));
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
