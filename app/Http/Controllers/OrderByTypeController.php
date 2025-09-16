<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Order;
use App\Models\Product;
use App\Models\Category;
use App\Models\Customer;
use App\Models\OrderItem;
use App\Models\Reservation;
use Illuminate\Http\Request;

class OrderByTypeController extends Controller
{
    public function createByType(Request $request)
    {
        $reservation = null;
        $today = Carbon::today();
        $orderType = $request->get('order_type', 'dine_in');

        // Only get reservation for dine-in orders
        if ($orderType === 'dine_in' && $request->reservation_id) {
            $reservation = Reservation::with(['room', 'customer'])->find($request->reservation_id);
        }

        $categories = Category::where('is_active', true)
            ->with(['products' => function ($q) {
                $q->where('is_available', true);
            }])
            ->orderBy('sort_order')
            ->get();
        $reservations = Reservation::where('reservation_date', '<=', $today)
            ->where('end_date', '>=', $today)
            ->whereIn('status', ['confirmed', 'checked_in'])
            ->count();

        $popularProducts = Product::where('is_popular', true)
            ->where('is_available', true)
            ->get();

        return view('orders.order', compact('reservations', 'categories', 'popularProducts', 'orderType'));
    }

    public function storeByType(Request $request)
    {
        // dd($request->all());
        $rules = [
            'order_type' => 'required|in:dine_in,takeaway,delivery',
            'products' => 'required|array',
            'products.*.id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
        ];

        // Add conditional validation based on order type
        if ($request->order_type === 'dine_in') {
            $rules['reservation_id'] = 'required|exists:reservations,id';
        } else {
            $rules['customer_name'] = 'required|string|max:255';
            $rules['customer_phone'] = 'required|string|max:20';

            if ($request->order_type === 'delivery') {
                $rules['delivery_address'] = 'required|string';
                $rules['delivery_fee'] = 'required|numeric|min:0';
            }
        }

        $request->validate($rules);

        // Handle customer for takeaway/delivery orders
        if (in_array($request->order_type, ['takeaway', 'delivery'])) {
            $customer = Customer::firstOrCreate(
                ['phone' => $request->customer_phone],
                [
                    'name' => $request->customer_name,
                    'email' => $request->customer_email
                ]
            );
            $customerId = $customer->id;
        } else {
            $reservation = Reservation::find($request->reservation_id);
            $customerId = $reservation->customer_id;
        }
        // dd($request->order_type);

        $order = Order::create([
            'order_type' => $request->order_type,
            'reservation_id' => $request->order_type === 'dine_in' ? $request->reservation_id : null,
            'customer_id' => $customerId,
            'customer_name' => $request->customer_name,
            'customer_phone' => $request->customer_phone,
            'delivery_address' => $request->delivery_address,
            'delivery_fee' => $request->delivery_fee ?? 0,
            'waiter_id' => auth()->id(),
            'subtotal' => 0,
            'tax_amount' => 0, // Set to 0
            'waiter_commission' => 0, // Will be calculated
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
}
