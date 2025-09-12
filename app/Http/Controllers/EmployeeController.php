<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class EmployeeController extends Controller
{
    public function index()
    {
        $employees = User::with('role')
                        ->orderBy('name')
                        ->paginate(20);

        $stats = [
            'total_employees' => User::count(),
            'active_employees' => User::where('is_active', true)->count(),
            'total_salary' => User::where('is_active', true)->sum('salary'),
            'avg_salary' => User::where('is_active', true)->avg('salary'),
        ];

        return view('employees.index', compact('employees', 'stats'));
    }

    public function create()
    {
        $roles = Role::all();
        return view('employees.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|string|max:20|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'role_id' => 'required|exists:roles,id',
            'salary' => 'required|numeric|min:0',
            'hire_date' => 'required|date',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role_id' => $request->role_id,
            'salary' => $request->salary,
            'hire_date' => $request->hire_date,
            'is_active' => true,
        ]);

        return redirect()->route('employees.index')
                        ->with('success', 'Xodim muvaffaqiyatli qo\'shildi!');
    }

    public function show(User $employee)
    {
        $employee->load(['role', 'orders', 'payments']);
        
        $stats = [
            'total_orders' => $employee->orders()->count(),
            'total_sales' => $employee->orders()->sum('total_amount'),
            'avg_order_value' => $employee->orders()->avg('total_amount'),
            'work_days' => $employee->created_at->diffInDays(now()),
        ];

        $recentOrders = $employee->orders()
                                ->with(['customer', 'reservation.room'])
                                ->orderBy('order_time', 'desc')
                                ->limit(10)
                                ->get();

        return view('employees.show', compact('employee', 'stats', 'recentOrders'));
    }

    public function edit(User $employee)
    {
        $roles = Role::all();
        return view('employees.edit', compact('employee', 'roles'));
    }

    public function update(Request $request, User $employee)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $employee->id,
            'phone' => 'required|string|max:20|unique:users,phone,' . $employee->id,
            'role_id' => 'required|exists:roles,id',
            'salary' => 'required|numeric|min:0',
            'hire_date' => 'required|date',
        ]);

        $data = $request->only(['name', 'email', 'phone', 'role_id', 'salary', 'hire_date']);

        if ($request->filled('password')) {
            $request->validate(['password' => 'string|min:6|confirmed']);
            $data['password'] = Hash::make($request->password);
        }

        $employee->update($data);

        return redirect()->route('employees.show', $employee)
                        ->with('success', 'Xodim ma\'lumotlari yangilandi!');
    }

    public function destroy(User $employee)
    {
        if ($employee->orders()->count() > 0 || $employee->payments()->count() > 0) {
            return back()->withErrors(['error' => 'Ushbu xodimning faoliyat tarixi mavjud, o\'chirib bo\'lmaydi!']);
        }

        $employee->delete();

        return redirect()->route('employees.index')
                        ->with('success', 'Xodim o\'chirildi!');
    }

    public function toggleStatus(User $employee)
    {
        $employee->update(['is_active' => !$employee->is_active]);
        
        $status = $employee->is_active ? 'faollashtirildi' : 'o\'chirildi';
        return back()->with('success', "Xodim {$status}!");
    }
}