<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\Category;
use App\Models\Customer;
use App\Models\OrderItem;
use App\Models\Reservation;
use Illuminate\Http\Request;

class QuickOrderController extends Controller
{
    public function index(Request $request)
    {
        $reservation_id = $request->get('reservation_id');
        $reservation = null;
        
        if ($reservation_id) {
            $reservation = Reservation::with(['customer', 'room'])->find($reservation_id);
        }

        $categories = Category::where('is_active', true)
                            ->with(['products' => function($query) {
                                $query->where('is_available', true)
                                      ->orderBy('is_popular', 'desc')
                                      ->orderBy('name_uz');
                            }])
                            ->orderBy('sort_order')
                            ->get();

        $popularProducts = Product::where('is_available', true)
                                 ->where('is_popular', true)
                                 ->with('category')
                                 ->take(6)
                                 ->get();

        return view('quick-order.index', compact('categories', 'popularProducts', 'reservation'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'reservation_id' => 'nullable|exists:reservations,id',
            'customer_name' => 'nullable|string|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'table_number' => 'nullable|string|max:10',
            'notes' => 'nullable|string',
        ]);

        // Create or find customer if not linked to reservation
        $customer = null;
        if ($request->reservation_id) {
            $reservation = Reservation::find($request->reservation_id);
            $customer = $reservation->customer;
        } else {
            // For walk-in customers
            if ($request->customer_name && $request->customer_phone) {
                $customer = Customer::firstOrCreate(
                    ['phone' => $request->customer_phone],
                    ['name' => $request->customer_name]
                );
            } else {
                // Create anonymous customer
                $customer = Customer::create([
                    'name' => 'Walk-in Customer #' . date('His'),
                    'phone' => 'N/A',
                ]);
            }
        }

        // Create order
        $order = Order::create([
            'reservation_id' => $request->reservation_id,
            'customer_id' => $customer->id,
            'waiter_id' => auth()->id(),
            'subtotal' => 0,
            'tax_amount' => 0,
            'total_amount' => 0,
            'status' => 'pending',
            'notes' => $request->notes,
            'table_number' => $request->table_number,
        ]);

        $subtotal = 0;
        
        // Add order items
        foreach ($request->items as $item) {
            $product = Product::find($item['product_id']);
            $itemTotal = $product->price * $item['quantity'];
            
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'quantity' => $item['quantity'],
                'unit_price' => $product->price,
                'total_price' => $itemTotal,
                'special_instructions' => $item['instructions'] ?? null,
            ]);
            
            $subtotal += $itemTotal;
        }

        // Calculate totals
        $taxAmount = $subtotal * 0.12; // 12% tax
        $totalAmount = $subtotal + $taxAmount;

        $order->update([
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'total_amount' => $totalAmount,
        ]);

        return response()->json([
            'success' => true,
            'order_id' => $order->id,
            'order_number' => $order->order_number,
            'total_amount' => $totalAmount,
            'redirect_url' => route('quick-order.receipt', $order),
        ]);
    }

    public function receipt(Order $order)
    {
        $order->load(['items.product.category', 'customer', 'reservation.room']);
        
        return view('quick-order.receipt', compact('order'));
    }

    public function printReceipt(Order $order)
    {
        $order->load(['items.product.category', 'customer', 'reservation.room']);
        
        return view('quick-order.print-receipt', compact('order'));
    }
}
