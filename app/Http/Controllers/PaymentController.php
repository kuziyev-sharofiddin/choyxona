<?php

// app/Http/Controllers/PaymentController.php
namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use App\Models\Reservation;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index()
    {
        $payments = Payment::with(['reservation.customer','reservation.room', 
        'order.customer', 
        'order.reservation.room',  'cashier'])
            ->orderBy('payment_time', 'desc')
            ->paginate(20);

        return view('payments.index', compact('payments'));
    }

    public function create(Request $request)
    {
        $reservation = null;
        $order = null;
        if ($request->reservation_id) {
            $reservation = Reservation::with(['customer', 'room', 'orders'])
                ->find($request->reservation_id);
        }
        if ($request->order_id) {
            $order = Order::with(['customer', 'reservation.room', 'items'])
                ->find($request->order_id);
        }

        return view('payments.create', compact('reservation', 'order'));
    }
    public function edit(Payment $payment)
    {
        if ($payment->status !== 'pending') {
            return back()->withErrors(['error' => 'Faqat jarayonda to\'lovlarni tahrirlash mumkin!']);
        }

        return view('payments.edit', compact('payment'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'reservation_id' => 'nullable|exists:reservations,id',
            'order_id' => 'nullable|exists:orders,id',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|in:cash,card,transfer',
        ]);
        // Must have either reservation_id or order_id
        if (!$request->reservation_id && !$request->order_id) {
            return back()->withErrors(['error' => 'Rezervatsiya yoki buyurtmani tanlang!']);
        }

        $payment = Payment::create([
            'reservation_id' => $request->reservation_id,
            'order_id' => $request->order_id,
            'amount' => $request->amount,
            'payment_method' => $request->payment_method,
            'cashier_id' => auth()->id(),
            'payment_time' => now(),
            'notes' => $request->notes,
            'status' => 'completed',
        ]);
        if ($request->reservation_id) {
            // Update customer total spent
            $reservation = Reservation::find($request->reservation_id);
            $reservation->customer->increment('total_spent', $request->amount);
        } elseif ($request->order_id) {
            // Update customer total spent
            $order = Order::find($request->order_id);
            $order->customer->increment('total_spent', $request->amount);
        }

        return redirect()->route('payments.show', $payment)
            ->with('success', 'To\'lov muvaffaqiyatli qabul qilindi!');
    }
    public function update(Request $request, Payment $payment)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|in:cash,card,transfer',
            'notes' => 'nullable|string',
        ]);

        if ($payment->status !== 'pending') {
            return back()->withErrors(['error' => 'Faqat jarayonda to\'lovlarni tahrirlash mumkin!']);
        }

        $payment->update($request->only(['amount', 'payment_method', 'notes']));

        return redirect()->route('payments.show', $payment)
            ->with('success', 'To\'lov ma\'lumotlari yangilandi!');
    }

    public function show(Payment $payment)
    {
        $payment->load(['reservation.customer', 'reservation.room','order.customer','order.reservation.room', 'cashier']);
        return view('payments.show', compact('payment'));
    }

    public function process(Payment $payment)
    {
        $payment->update(['status' => 'completed']);
        return back()->with('success', 'To\'lov qayta ishlab chiqildi!');
    }
}
