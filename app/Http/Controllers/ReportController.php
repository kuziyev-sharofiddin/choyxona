<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\User;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index()
    {
        $todayRevenue = Payment::whereDate('payment_time', today())
            ->where('status', 'completed')
            ->sum('amount');

        $todayOrders = Order::whereDate('order_time', today())->count();

        $thisMonthRevenue = Payment::whereMonth('payment_time', now()->month)
            ->whereYear('payment_time', now()->year)
            ->where('status', 'completed')
            ->sum('amount');

        $totalCustomers = Customer::count();

        return view('reports.index', compact(
            'todayRevenue',
            'todayOrders',
            'thisMonthRevenue',
            'totalCustomers'
        ));
    }

    public function daily()
    {
        $date = request('date', today()->format('Y-m-d'));

        $revenue = Payment::whereDate('payment_time', $date)
            ->where('status', 'completed')
            ->sum('amount');

        $orders = Order::whereDate('order_time', $date)->count();

        $reservations = Reservation::whereDate('reservation_date', $date)->count();

        $customers = Reservation::whereDate('reservation_date', $date)
            ->distinct('customer_id')
            ->count();

        $topProducts = Product::select('products.*')
            ->join('order_items', 'products.id', '=', 'order_items.product_id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereDate('orders.order_time', $date)
            ->groupBy('products.id')
            ->selectRaw('SUM(order_items.quantity) as total_quantity')
            ->orderBy('total_quantity', 'desc')
            ->limit(10)
            ->get();

        $hourlyData = Order::whereDate('order_time', $date)
            ->selectRaw('HOUR(order_time) as hour, COUNT(*) as orders_count, SUM(total_amount) as revenue')
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();

        return view('reports.daily', compact(
            'date',
            'revenue',
            'orders',
            'reservations',
            'customers',
            'topProducts',
            'hourlyData'
        ));
    }

    public function monthly()
    {
        $month = request('month', now()->format('Y-m'));
        $startDate = $month . '-01';
        $endDate = date('Y-m-t', strtotime($startDate));

        $revenue = Payment::whereBetween('payment_time', [$startDate, $endDate])
            ->where('status', 'completed')
            ->sum('amount');

        $orders = Order::whereBetween('order_time', [$startDate, $endDate])->count();

        $reservations = Reservation::whereBetween('reservation_date', [$startDate, $endDate])->count();

        $customers = Reservation::whereBetween('reservation_date', [$startDate, $endDate])
            ->distinct('customer_id')
            ->count();

        $dailyData = Payment::whereBetween('payment_time', [$startDate, $endDate])
            ->where('status', 'completed')
            ->selectRaw('DATE(payment_time) as date, SUM(amount) as revenue')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $topCustomers = Customer::select('customers.*')
            ->join('reservations', 'customers.id', '=', 'reservations.customer_id')
            ->join('payments', 'reservations.id', '=', 'payments.reservation_id')
            ->whereBetween('payments.payment_time', [$startDate, $endDate])
            ->where('payments.status', 'completed')
            ->groupBy('customers.id')
            ->selectRaw('SUM(payments.amount) as total_spent')
            ->orderBy('total_spent', 'desc')
            ->limit(10)
            ->get();

        return view('reports.monthly', compact(
            'month',
            'revenue',
            'orders',
            'reservations',
            'customers',
            'dailyData',
            'topCustomers'
        ));
    }

    public function products()
    {
        $period = request('period', '30');
        $startDate = now()->subDays($period);

        $topProducts = Product::select('products.*')
            ->join('order_items', 'products.id', '=', 'order_items.product_id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.order_time', '>=', $startDate)
            ->groupBy('products.id')
            ->selectRaw('SUM(order_items.quantity) as total_quantity, SUM(order_items.total_price) as total_revenue')
            ->orderBy('total_quantity', 'desc')
            ->paginate(20);

        $categoryStats = Product::select('categories.name_uz as category_name')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->join('order_items', 'products.id', '=', 'order_items.product_id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.order_time', '>=', $startDate)
            ->groupBy('categories.id', 'categories.name_uz')
            ->selectRaw('SUM(order_items.quantity) as total_quantity, SUM(order_items.total_price) as total_revenue')
            ->orderBy('total_revenue', 'desc')
            ->get();

        return view('reports.products', compact('topProducts', 'categoryStats', 'period'));
    }

    public function employees()
    {
        $period = request('period', '30');
        $startDate = now()->subDays($period);

        $employeeStats = User::select('users.*')
            ->join('orders', 'users.id', '=', 'orders.waiter_id')
            ->where('orders.order_time', '>=', $startDate)
            ->groupBy('users.id')
            ->selectRaw('COUNT(orders.id) as total_orders, SUM(orders.total_amount) as total_sales')
            ->orderBy('total_sales', 'desc')
            ->get();

        return view('reports.employees', compact('employeeStats', 'period'));
    }

    // Add missing methods to ReportController
    public function custom(Request $request)
    {
        $type = $request->type;
        $startDate = $request->start_date;
        $endDate = $request->end_date;

        $data = [];
        $title = '';

        switch ($type) {
            case 'revenue':
                $title = 'Daromad Hisoboti';
                $data = Payment::whereBetween('payment_time', [$startDate, $endDate])
                    ->where('status', 'completed')
                    ->selectRaw('DATE(payment_time) as date, SUM(amount) as revenue')
                    ->groupBy('date')
                    ->orderBy('date')
                    ->get();
                break;

            case 'orders':
                $title = 'Buyurtmalar Hisoboti';
                $data = Order::whereBetween('order_time', [$startDate, $endDate])
                    ->selectRaw('DATE(order_time) as date, COUNT(*) as orders_count')
                    ->groupBy('date')
                    ->orderBy('date')
                    ->get();
                break;

            case 'customers':
                $title = 'Mijozlar Hisoboti';
                $data = Customer::whereBetween('created_at', [$startDate, $endDate])
                    ->selectRaw('DATE(created_at) as date, COUNT(*) as new_customers')
                    ->groupBy('date')
                    ->orderBy('date')
                    ->get();
                break;

            case 'products':
                $title = 'Mahsulotlar Hisoboti';
                $data = Product::select('products.*')
                    ->join('order_items', 'products.id', '=', 'order_items.product_id')
                    ->join('orders', 'order_items.order_id', '=', 'orders.id')
                    ->whereBetween('orders.order_time', [$startDate, $endDate])
                    ->groupBy('products.id')
                    ->selectRaw('SUM(order_items.quantity) as total_quantity, SUM(order_items.total_price) as total_revenue')
                    ->orderBy('total_revenue', 'desc')
                    ->get();
                break;

            case 'rooms':
                $title = 'Xonalar Hisoboti';
                $data = Room::select('rooms.*')
                    ->join('reservations', 'rooms.id', '=', 'reservations.room_id')
                    ->whereBetween('reservations.reservation_date', [$startDate, $endDate])
                    ->groupBy('rooms.id')
                    ->selectRaw('COUNT(reservations.id) as total_bookings, SUM(reservations.room_charge) as total_revenue')
                    ->orderBy('total_revenue', 'desc')
                    ->get();
                break;
        }

        return view('reports.custom', compact('data', 'type', 'title', 'startDate', 'endDate'));
    }
}
