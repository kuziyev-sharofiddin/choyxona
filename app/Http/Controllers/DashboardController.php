<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Room;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Reservation;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $todayRevenue = Payment::whereDate('payment_time', today())
            ->where('status', 'completed')
            ->sum('amount');

            $yesterdayRevenue = Payment::whereDate('payment_time', Carbon::yesterday())
            ->where('status', 'completed')
            ->sum('amount');

        $activeReservations = Reservation::whereIn('status', ['confirmed', 'checked_in'])
            ->count();

        $todayOrders = Order::whereDate('order_time', today())->count();

        $todayOrderItems = \App\Models\OrderItem::whereHas('order', function ($q) {
            $q->whereDate('order_time', today());
        })->sum('quantity');

        $totalRooms = Room::count();
        $availableRooms = Room::where('status', 'available')->count();

        $rooms = Room::with(['currentReservation.customer'])->get();

        $recentOrders = Order::with(['customer', 'reservation.room', 'items'])
            ->orderBy('order_time', 'desc')
            ->limit(5)
            ->get();

        $todayReservations = Reservation::with(['customer', 'room'])
            ->whereDate('start_time', today())
            ->orderBy('start_time')
            ->limit(5)
            ->get();

        return view('dashboard', compact(
            'todayRevenue',
            'yesterdayRevenue',
            'activeReservations',
            'todayOrders',
            'todayOrderItems',
            'totalRooms',
            'availableRooms',
            'rooms',
            'recentOrders',
            'todayReservations'
        ));
    }
    public function dashboardStats()
    {
        $data = [
            'todayRevenue' => Payment::whereDate('payment_time', today())
                ->where('status', 'completed')
                ->sum('amount'),
            'activeReservations' => Reservation::whereIn('status', ['confirmed', 'checked_in'])
                ->count(),
            'todayOrders' => Order::whereDate('order_time', today())->count(),
            'availableRooms' => Room::where('status', 'available')->count(),
            'totalRooms' => Room::count(),
        ];

        return response()->json($data);
    }
}
