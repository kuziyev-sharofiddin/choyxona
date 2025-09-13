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
        $today = Carbon::today();
        $yesterday = Carbon::yesterday();

        // Bugungi daromad (to'lovlar orqali)
        $todayRevenue = Payment::whereDate('created_at', $today)
            ->where('status', 'completed')
            ->sum('amount');
            
        // Kechagi daromad
        $yesterdayRevenue = Payment::whereDate('created_at', $yesterday)
            ->where('status', 'completed')
            ->sum('amount');

        // Aktiv rezervatsiyalar (bugun faol bo'lgan)
        $activeReservations = Reservation::where('reservation_date', '<=', $today)
            ->where('end_date', '>=', $today)
            ->whereIn('status', ['confirmed', 'checked_in'])
            ->count();

        // Bugungi buyurtmalar
        $todayOrders = Order::whereDate('created_at', $today)->count();

        // Bugungi buyurtma mahsulotlari soni
        $todayOrderItems = \App\Models\OrderItem::whereHas('order', function ($q) use ($today) {
            $q->whereDate('created_at', $today);
        })->sum('quantity');

        // Xonalar statistikasi
        $totalRooms = Room::count();
        $availableRooms = Room::where('status', 'available')->count();
        $occupiedRooms = Room::where('status', 'occupied')->count();

        // Xonalar va ularning holati
        $rooms = Room::with(['reservations' => function($query) use ($today) {
            $query->where('reservation_date', '<=', $today)
                  ->where('end_date', '>=', $today)
                  ->where('status', 'confirmed')
                  ->with('customer');
        }])->get();

        // So'nggi buyurtmalar
        $recentOrders = Order::with(['reservation.customer', 'reservation.room', 'items.product'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Bugungi rezervatsiyalar
        $todayReservations = Reservation::with(['customer', 'room', 'user'])
            ->where('reservation_date', $today)
            ->orderBy('created_at')
            ->limit(5)
            ->get();

        // Bugun boshlanadigan rezervatsiyalar
        $todayStarting = Reservation::with(['customer', 'room'])
            ->where('reservation_date', $today)
            ->where('status', 'confirmed')
            ->get();

        // Bugun tugaydigan rezervatsiyalar  
        $todayEnding = Reservation::with(['customer', 'room'])
            ->where('end_date', $today)
            ->where('status', 'confirmed')
            ->get();

        // Bu oylik statistika
        $thisMonth = Carbon::now()->startOfMonth();
        $monthlyRevenue = Payment::where('created_at', '>=', $thisMonth)
            ->where('status', 'completed')
            ->sum('amount');

        $monthlyReservations = Reservation::where('created_at', '>=', $thisMonth)
            ->where('status', '!=', 'cancelled')
            ->count();

        // Mashhur xonalar (eng ko'p band bo'ladigan)
        $popularRooms = Room::withCount(['reservations as monthly_bookings' => function($query) use ($thisMonth) {
                $query->where('created_at', '>=', $thisMonth)
                      ->where('status', '!=', 'cancelled');
            }])
            ->orderBy('monthly_bookings', 'desc')
            ->take(5)
            ->get();

        // Haftalik statistika
        $weeklyStats = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $weeklyStats[] = [
                'date' => $date->format('D, d M'),
                'reservations' => Reservation::where('reservation_date', '<=', $date)
                    ->where('end_date', '>=', $date)
                    ->where('status', 'confirmed')
                    ->count(),
                'revenue' => Payment::whereDate('created_at', $date)
                    ->where('status', 'completed')
                    ->sum('amount'),
            ];
        }

        return view('dashboard', compact(
            'todayRevenue',
            'yesterdayRevenue',
            'activeReservations',
            'todayOrders',
            'todayOrderItems',
            'totalRooms',
            'availableRooms',
            'occupiedRooms',
            'rooms',
            'recentOrders',
            'todayReservations',
            'todayStarting',
            'todayEnding',
            'monthlyRevenue',
            'monthlyReservations',
            'popularRooms',
            'weeklyStats'
        ));
    }

    public function dashboardStats()
    {
        $today = Carbon::today();
        
        $data = [
            'todayRevenue' => Payment::whereDate('created_at', $today)
                ->where('status', 'completed')
                ->sum('amount'),
            'activeReservations' => Reservation::where('reservation_date', '<=', $today)
                ->where('end_date', '>=', $today)
                ->whereIn('status', ['confirmed', 'checked_in'])
                ->count(),
            'todayOrders' => Order::whereDate('created_at', $today)->count(),
            'availableRooms' => Room::where('status', 'available')->count(),
            'totalRooms' => Room::count(),
            'occupiedRooms' => Room::where('status', 'occupied')->count(),
            'todayStarting' => Reservation::where('reservation_date', $today)
                ->where('status', 'confirmed')
                ->count(),
            'todayEnding' => Reservation::where('end_date', $today)
                ->where('status', 'confirmed')
                ->count(),
        ];

        return response()->json($data);
    }

    // AJAX uchun xonalar holati
    public function roomsStatus()
    {
        $today = Carbon::today();
        
        $rooms = Room::with(['reservations' => function($query) use ($today) {
            $query->where('reservation_date', '<=', $today)
                  ->where('end_date', '>=', $today)
                  ->where('status', 'confirmed')
                  ->with('customer');
        }])->get();

        return response()->json($rooms);
    }

    // Haftalik chart ma'lumotlari
    public function weeklyChart()
    {
        $data = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            
            $data[] = [
                'date' => $date->format('M d'),
                'reservations' => Reservation::where('reservation_date', '<=', $date)
                    ->where('end_date', '>=', $date)
                    ->where('status', 'confirmed')
                    ->count(),
                'revenue' => Payment::whereDate('created_at', $date)
                    ->where('status', 'completed')
                    ->sum('amount'),
                'orders' => Order::whereDate('created_at', $date)->count(),
            ];
        }
        
        return response()->json($data);
    }
}
