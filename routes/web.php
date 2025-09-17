<?php
// routes/web.php
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\OrderByTypeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\RoomController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Authentication Routes
Route::get('/', function () {
    return redirect('/login');
});

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Protected Routes
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/api/dashboard/stats', [DashboardController::class, 'dashboardStats'])->name('dashboard.stats');
    
    // Reservations
    Route::resource('reservations', ReservationController::class);
    Route::post('reservations/{reservation}/checkin', [ReservationController::class, 'checkIn'])->name('reservations.checkin');
    Route::post('reservations/{reservation}/complete', [ReservationController::class, 'complete'])->name('reservations.complete');
    Route::get('reservations/export', [ReservationController::class, 'export'])->name('reservations.export');
    Route::post('reservations/bulk-update', [ReservationController::class, 'bulkUpdate'])->name('reservations.bulk-update');
    Route::post('reservations/bulk-export', [ReservationController::class, 'bulkExport'])->name('reservations.bulk-export');
    Route::get('reservations/{reservation}/receipt', [ReservationController::class, 'receipt'])->name('reservations.receipt');
    
    // Rooms
    Route::resource('rooms', RoomController::class);
    Route::post('rooms/{room}/maintenance', [RoomController::class, 'setMaintenance'])->name('rooms.maintenance');
    Route::get('rooms/{room}/availability', [RoomController::class, 'checkAvailability'])->name('rooms.availability');
    
    // Orders
    Route::resource('orders', OrderController::class);
    Route::get('kitchen', [OrderController::class, 'kitchen'])->name('orders.kitchen');
    Route::post('orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.update-status');
    Route::get('create-by-type', [OrderByTypeController::class, 'createByType'])->name('create_order_by_type');
    Route::post('store-by-type', [OrderByTypeController::class, 'storeByType'])->name('store_order_by_type');
    Route::get('orders/returned/list', [OrderController::class, 'returnedItems'])->name('orders.returned');
    
    // Products
    Route::resource('products', ProductController::class);
    Route::post('products/{product}/toggle', [ProductController::class, 'toggleAvailability'])->name('products.toggle');
    
    // Categories
    Route::resource('categories', CategoryController::class);
    
    // Customers
    Route::resource('customers', CustomerController::class);
    
    // Employees
    Route::resource('employees', EmployeeController::class);
    Route::post('employees/{employee}/toggle', [EmployeeController::class, 'toggleStatus'])->name('employees.toggle');
    
    // Payments
    Route::resource('payments', PaymentController::class);
    Route::post('payments/{payment}/process', [PaymentController::class, 'process'])->name('payments.process');
    
    // Reports
    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/daily', [ReportController::class, 'daily'])->name('reports.daily');
    Route::get('reports/monthly', [ReportController::class, 'monthly'])->name('reports.monthly');
    Route::get('reports/products', [ReportController::class, 'products'])->name('reports.products');
    Route::get('reports/employees', [ReportController::class, 'employees'])->name('reports.employees');
    Route::get('reports/custom', [ReportController::class, 'custom'])->name('reports.custom');
    
    // API Routes for AJAX
    Route::prefix('api')->group(function () {
        Route::get('rooms/available', [RoomController::class, 'getAvailableRooms']);
        Route::get('products/search', [ProductController::class, 'search']);
        Route::get('customers/search', [CustomerController::class, 'search']);
        Route::get('orders/status/{status}', [OrderController::class, 'getOrdersByStatus']);
    });
    // Add missing routes
});