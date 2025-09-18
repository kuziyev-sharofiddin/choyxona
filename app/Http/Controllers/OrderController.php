<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\Category;
use App\Models\Customer;
use App\Models\OrderItem;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        // Load all necessary relationships
        $order->load(['reservation.room', 'customer', 'waiter', 'items.product.category']);
        
        return view('orders.edit', compact('order'));
    }
    public function update(Request $request, Order $order)
    {
        // Validate basic order data
        $request->validate([
            'status' => 'required|in:pending,preparing,ready,served,completed',
            'discount_amount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
            'items' => 'nullable|array',
            'items.*.product_id' => 'required_with:items|exists:products,id',
            'items.*.quantity' => 'required_with:items|integer|min:0',
            'items.*.unit_price' => 'required_with:items|numeric|min:0',
            'items.*.status' => 'required_with:items|in:pending,preparing,ready,served'
        ]);

        DB::beginTransaction();
        
        try {
            // Check if order can be edited
            $canEditItems = in_array($order->status, ['pending']) && 
                           in_array($request->status, ['pending', 'preparing']);
            
            // Update basic order info
            $order->update([
                'status' => $request->status,
                'discount_amount' => $request->discount_amount ?? 0,
                'notes' => $request->notes,
            ]);

            // Handle order items only if editing is allowed
            if ($canEditItems && $request->has('items')) {
                // Get current item IDs
                $currentItemIds = $order->items->pluck('id')->toArray();
                $updatedItemIds = [];

                foreach ($request->items as $itemData) {
                    if (isset($itemData['quantity']) && $itemData['quantity'] > 0) {
                        if (isset($itemData['id']) && $itemData['id']) {
                            // Update existing item
                            $item = OrderItem::find($itemData['id']);
                            if ($item && $item->order_id == $order->id) {
                                $item->update([
                                    'quantity' => $itemData['quantity'],
                                    'total_price' => $itemData['unit_price'] * $itemData['quantity'],
                                    'status' => $itemData['status'] ?? 'pending'
                                ]);
                                $updatedItemIds[] = $item->id;
                            }
                        } else {
                            // Create new item
                            $newItem = OrderItem::create([
                                'order_id' => $order->id,
                                'product_id' => $itemData['product_id'],
                                'quantity' => $itemData['quantity'],
                                'unit_price' => $itemData['unit_price'],
                                'total_price' => $itemData['unit_price'] * $itemData['quantity'],
                                'status' => $itemData['status'] ?? 'pending'
                            ]);
                            $updatedItemIds[] = $newItem->id;
                        }
                    }
                }

                // Remove items that were deleted
                $itemsToDelete = array_diff($currentItemIds, $updatedItemIds);
                if (!empty($itemsToDelete)) {
                    OrderItem::whereIn('id', $itemsToDelete)->delete();
                }
            } elseif (!$canEditItems && $request->has('items')) {
                // Only update status of existing items if order can't be fully edited
                foreach ($request->items as $itemData) {
                    if (isset($itemData['id']) && $itemData['id']) {
                        $item = OrderItem::find($itemData['id']);
                        if ($item && $item->order_id == $order->id) {
                            $item->update([
                                'status' => $itemData['status'] ?? $item->status
                            ]);
                        }
                    }
                }
            }

            // Recalculate totals
            $this->recalculateOrderTotals($order);

            // If order is completed, update customer stats
            if ($request->status === 'completed' && $order->status !== 'completed') {
                $customer = $order->customer;
                $customer->increment('total_spent', $order->total_amount);
            }

            DB::commit();

            return redirect()->route('orders.show', $order)
                           ->with('success', 'Buyurtma muvaffaqiyatli yangilandi!');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Buyurtmani yangilashda xatolik: ' . $e->getMessage()]);
        }
    }
    private function recalculateOrderTotals(Order $order)
{
    // Elementlar summasi
    $subtotal = $order->items()->sum('total_price');
    
    $taxAmount = 0; // No tax
    
    // Use config for commission rate
    $commissionRate = config('choyxona.waiter_commission_rate', 0.10);
    
    // Commission faqat dine_in uchun
    $waiterCommission = $order->order_type === 'dine_in' ? $subtotal * $commissionRate : 0;
    
    // Delivery fee faqat delivery uchun (ikki marta $ belgisi xato edi)
    $additionalFee = $order->order_type === 'delivery' ? $order->delivery_fee : 0;
    
    // Umumiy summani hisoblash
    $totalAmount = $subtotal + $additionalFee + $waiterCommission - ($order->discount_amount ?? 0);
    
    // Barcha ma'lumotlarni yangilash
    $order->update([
        'subtotal' => $subtotal,
        'tax_amount' => $taxAmount,
        'waiter_commission' => $waiterCommission,
        'total_amount' => $totalAmount
    ]);
}
    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,preparing,ready,served,completed'
        ]);

        $oldStatus = $order->status;
        $order->update(['status' => $request->status]);

        // Set served time when status changes to served
        if ($request->status === 'served' && $oldStatus !== 'served') {
            $order->update(['served_time' => now()]);
        }

        // Update customer stats when completed
        if ($request->status === 'completed' && $oldStatus !== 'completed') {
            $order->customer->increment('total_spent', $order->total_amount);
        }

        return response()->json(['success' => true, 'message' => 'Holat yangilandi']);
    }
    public function quickStatusUpdate(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,preparing,ready,served,completed'
        ]);

        $order->update(['status' => $request->status]);

        if ($request->status === 'served') {
            $order->update(['served_time' => now()]);
        }

        return response()->json([
            'success' => true, 
            'message' => 'Buyurtma holati yangilandi',
            'new_status' => $request->status
        ]);
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
    public function bulkStatusUpdate(Request $request)
    {
        $request->validate([
            'order_ids' => 'required|array',
            'order_ids.*' => 'exists:orders,id',
            'status' => 'required|in:preparing,ready,served'
        ]);

        $updated = Order::whereIn('id', $request->order_ids)
                       ->update(['status' => $request->status]);

        // Set served time for orders marked as served
        if ($request->status === 'served') {
            Order::whereIn('id', $request->order_ids)
                 ->whereNull('served_time')
                 ->update(['served_time' => now()]);
        }

        return response()->json([
            'success' => true,
            'message' => "{$updated} ta buyurtma holati yangilandi"
        ]);
    }
    public function getOrderStats()
    {
        $today = now()->toDateString();
        
        $stats = [
            'today_orders' => Order::whereDate('order_time', $today)->count(),
            'today_revenue' => Order::whereDate('order_time', $today)
                                   ->where('status', 'completed')
                                   ->sum('total_amount'),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'preparing_orders' => Order::where('status', 'preparing')->count(),
            'ready_orders' => Order::where('status', 'ready')->count(),
            'avg_order_value' => Order::whereDate('order_time', $today)
                                     ->avg('total_amount'),
        ];

        return response()->json($stats);
    }
    private function canEditOrder(Order $order, $newStatus = null)
    {
        $checkStatus = $newStatus ?? $order->status;
        
        // Can edit items only in pending status
        $canEditItems = $order->status === 'pending';
        
        // Can change status in pending and preparing
        $canChangeStatus = in_array($order->status, ['pending', 'preparing']);
        
        // Completed orders cannot be edited at all
        $canEdit = $order->status !== 'completed';

        return [
            'can_edit' => $canEdit,
            'can_edit_items' => $canEditItems,
            'can_change_status' => $canChangeStatus,
            'reason' => $this->getEditRestrictionReason($order->status)
        ];
    }
    private function getEditRestrictionReason($status)
    {
        switch ($status) {
            case 'completed':
                return 'Tugallangan buyurtmalarni tahrirlash mumkin emas';
            case 'served':
                return 'Berilgan buyurtmalarda faqat holat o\'zgartirish mumkin';
            case 'ready':
                return 'Tayyor buyurtmalarda faqat holat o\'zgartirish mumkin';
            case 'preparing':
                return 'Tayyorlanayotgan buyurtmalarda faqat holat o\'zgartirish mumkin';
            default:
                return null;
        }
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
