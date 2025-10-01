<?php

// app/Http/Controllers/CustomerController.php
namespace App\Http\Controllers;

use DB;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB as FacadesDB;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = Customer::withCount('reservations');

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('phone', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }

        // Sorting
        $sort = $request->get('sort', 'last_visit');
        switch ($sort) {
            case 'total_spent':
                $query->orderBy('total_spent', 'desc');
                break;
            case 'visit_count':
                $query->orderBy('visit_count', 'desc');
                break;
            case 'name':
                $query->orderBy('name', 'asc');
                break;
            default:
                $query->orderBy('last_visit', 'desc');
                break;
        }

        $customers = $query->paginate(20);

        return view('customers.index', compact('customers'));
    }

    public function create()
    {
        return view('customers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20|unique:customers',
            'email' => 'nullable|email|unique:customers',
        ]);

        Customer::create($request->all());

        return redirect()->route('customers.index')
                        ->with('success', 'Mijoz muvaffaqiyatli qo\'shildi!');
    }

    public function show(Customer $customer)
{
    $customer->load(['reservations.room', 'orders.items.product']);
    
    $stats = [
        'total_visits' => $customer->visit_count,
        'total_spent' => $customer->total_spent,
        'avg_spending' => $customer->visit_count > 0 ? $customer->total_spent / $customer->visit_count : 0,
        'last_visit' => $customer->last_visit,
        'favorite_products' => $customer->orders()
                                      ->join('order_items', 'orders.id', '=', 'order_items.order_id')
                                      ->join('products', 'order_items.product_id', '=', 'products.id')
                                      ->select('products.name_uz', \DB::raw('SUM(order_items.quantity) as total_quantity'))
                                      ->groupBy('products.id', 'products.name_uz')
                                      ->orderBy('total_quantity', 'desc')
                                      ->limit(5)
                                      ->get(),
    ];

    return view('customers.show', compact('customer', 'stats'));
}

    public function edit(Customer $customer)
    {
        return view('customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20|unique:customers,phone,' . $customer->id,
            'email' => 'nullable|email|unique:customers,email,' . $customer->id,
        ]);

        $customer->update($request->all());

        return redirect()->route('customers.show', $customer)
                        ->with('success', 'Mijoz ma\'lumotlari yangilandi!');
    }

    public function search(Request $request)
    {
        $query = $request->get('q');
        
        $customers = Customer::where('name', 'LIKE', "%{$query}%")
                           ->orWhere('phone', 'LIKE', "%{$query}%")
                           ->limit(10)
                           ->get();

        return response()->json($customers);
    }
}