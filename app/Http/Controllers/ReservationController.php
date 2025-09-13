<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Room;
use App\Models\User;
use App\Models\Customer;
use App\Models\Reservation;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    public function index(Request $request)
    {
        $query = Reservation::with(['customer', 'room', 'user']);

        // Qidiruv filterlari
        if ($request->search) {
            $query->whereHas('customer', function($q) use ($request) {
                $q->where('name', 'LIKE', '%' . $request->search . '%')
                  ->orWhere('phone', 'LIKE', '%' . $request->search . '%');
            });
        }

        if ($request->room_id) {
            $query->where('room_id', $request->room_id);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->date) {
            $query->where('reservation_date', $request->date);
        }

        $reservations = $query->orderBy('reservation_date', 'desc')
                             ->paginate(20);

        $rooms = Room::all();
        
        return view('reservations.index', compact('reservations', 'rooms'));
    }

    public function create(Request $request)
    {
        $rooms = Room::where('status', 'available')->get();
        $waiters = User::whereHas('role', function($q) {
            $q->where('name', 'waiter');
        })->where('is_active', true)->get();
        
        $selectedRoom = null;
        if($request->room_id) {
            $selectedRoom = Room::find($request->room_id);
        }

        return view('reservations.create', compact('rooms', 'waiters', 'selectedRoom'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'room_id' => 'required|exists:rooms,id',
            'reservation_date' => 'required|date|after_or_equal:today',
            'days_count' => 'required|integer|min:1|max:30',
            'guest_count' => 'required|integer|min:1',
            'user_id' => 'required|exists:users,id',
        ]);

        // Xonani topish
        $room = Room::find($request->room_id);
        
        // Xona mavjudligini tekshirish
        if (!$room->isAvailableForDate($request->reservation_date, $request->days_count)) {
            return back()->withErrors(['reservation_date' => 'Bu kunlarda xona band!']);
        }

        // Mijozni yaratish yoki topish
        $customer = Customer::firstOrCreate(
            ['phone' => $request->customer_phone],
            ['name' => $request->customer_name, 'email' => $request->customer_email]
        );

        // Xona to'lovini hisoblash
        $roomCharge = $room->daily_rate * $request->days_count;

        // Tugash sanasini hisoblash
        $endDate = Carbon::parse($request->reservation_date)
                        ->addDays($request->days_count - 1)
                        ->format('Y-m-d');

        // Rezervatsiya yaratish
        $reservation = Reservation::create([
            'customer_id' => $customer->id,
            'room_id' => $request->room_id,
            'user_id' => $request->user_id,
            'reservation_date' => $request->reservation_date,
            'days_count' => $request->days_count,
            'end_date' => $endDate,
            'guest_count' => $request->guest_count,
            'room_charge' => $roomCharge,
            'special_requests' => $request->special_requests,
            'status' => 'confirmed'
        ]);

        // Xona holatini yangilash (agar bugun boshlansa)
        if (Carbon::parse($request->reservation_date)->isToday()) {
            $room->update(['status' => 'occupied']);
        }

        return redirect()->route('reservations.show', $reservation)
                        ->with('success', 'Rezervatsiya muvaffaqiyatli yaratildi!');
    }

    public function show(Reservation $reservation)
    {
        $reservation->load(['customer', 'room', 'user', 'orders.items.product', 'payments']);
        return view('reservations.show', compact('reservation'));
    }

    public function edit(Reservation $reservation)
    {
        if ($reservation->status === 'completed' || $reservation->is_expired) {
            return redirect()->route('reservations.show', $reservation)
                           ->with('error', 'Bu rezervatsiyani tahrirlash mumkin emas!');
        }

        $rooms = Room::all();
        $waiters = User::whereHas('role', function($q) {
            $q->where('name', 'waiter');
        })->where('is_active', true)->get();

        return view('reservations.edit', compact('reservation', 'rooms', 'waiters'));
    }

    public function update(Request $request, Reservation $reservation)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'room_id' => 'required|exists:rooms,id',
            'reservation_date' => 'required|date|after_or_equal:today',
            'days_count' => 'required|integer|min:1|max:30',
            'guest_count' => 'required|integer|min:1',
            'status' => 'required|in:confirmed,cancelled,completed',
        ]);

        // Mijoz ma'lumotlarini yangilash
        $reservation->customer->update([
            'name' => $request->customer_name,
            'phone' => $request->customer_phone,
            'email' => $request->customer_email,
        ]);

        // Xona to'lovini qayta hisoblash
        $room = Room::find($request->room_id);
        $roomCharge = $room->daily_rate * $request->days_count;
        $endDate = Carbon::parse($request->reservation_date)
                        ->addDays($request->days_count - 1)
                        ->format('Y-m-d');

        // Rezervatsiyani yangilash
        $reservation->update([
            'room_id' => $request->room_id,
            'reservation_date' => $request->reservation_date,
            'days_count' => $request->days_count,
            'end_date' => $endDate,
            'guest_count' => $request->guest_count,
            'room_charge' => $roomCharge,
            'special_requests' => $request->special_requests,
            'status' => $request->status,
        ]);

        return redirect()->route('reservations.show', $reservation)
                        ->with('success', 'Rezervatsiya muvaffaqiyatli yangilandi!');
    }

    public function checkAvailability(Request $request)
    {
        $room = Room::find($request->room_id);
        $excludeId = $request->exclude_reservation_id ?? null;
        
        $isAvailable = $room->isAvailableForDate(
            $request->date, 
            $request->days_count ?? 1,
            $excludeId  // Bu rezervatsiyani hisobga olmaslik
        );
        
        return response()->json([
            'available' => $isAvailable,
            'daily_rate' => $room->daily_rate,
            'total_charge' => $room->daily_rate * ($request->days_count ?? 1)
        ]);
    }

    public function checkIn(Reservation $reservation)
    {
        $reservation->update(['status' => 'checked_in']);
        return back()->with('success', 'Mijoz keldi deb belgilandi!');
    }

    public function complete(Reservation $reservation)
    {
        $reservation->update(['status' => 'completed']);
        $reservation->room->update(['status' => 'available']);

        return back()->with('success', 'Rezervatsiya tugallandi!');
    }

    public function destroy(Reservation $reservation)
    {
        if ($reservation->orders()->count() > 0) {
            return back()->withErrors(['error' => 'Buyurtmalari bo\'lgan rezervatsiyani o\'chirib bo\'lmaydi!']);
        }

        $reservation->room->updateStatusBasedOnReservations();
        $reservation->delete();

        return redirect()->route('reservations.index')
            ->with('success', 'Rezervatsiya o\'chirildi!');
    }
    public function export(Request $request)
    {
        $query = Reservation::with(['customer', 'room', 'waiter', 'orders.items.product']);

        // Apply same filters as index
        if ($request->search) {
            $query->whereHas('customer', function ($q) use ($request) {
                $q->where('name', 'LIKE', "%{$request->search}%")
                    ->orWhere('phone', 'LIKE', "%{$request->search}%");
            });
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->room_id) {
            $query->where('room_id', $request->room_id);
        }

        if ($request->date) {
            $query->whereDate('start_time', $request->date);
        }

        $reservations = $query->orderBy('start_time', 'desc')->get();

        // Create Excel file
        $filename = 'rezervatsiyalar_' . date('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($reservations) {
            $file = fopen('php://output', 'w');

            // Add BOM for UTF-8
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Headers
            fputcsv($file, [
                'Rezervatsiya №',
                'Mijoz Ismi',
                'Telefon',
                'Email',
                'Xona',
                'Boshlanish Vaqti',
                'Tugash Vaqti',
                'Mehmonlar Soni',
                'Xona Narxi',
                'Buyurtma Narxi',
                'Umumiy Summa',
                'Holat',
                'Ofitsiant',
                'Yaratilgan'
            ]);

            // Data
            foreach ($reservations as $reservation) {
                fputcsv($file, [
                    $reservation->reservation_number,
                    $reservation->customer->name,
                    $reservation->customer->phone,
                    $reservation->customer->email,
                    $reservation->room->name_uz,
                    $reservation->start_time->format('d.m.Y H:i'),
                    $reservation->end_time->format('d.m.Y H:i'),
                    $reservation->guest_count,
                    number_format($reservation->room_charge, 0, '.', ''),
                    number_format($reservation->orders->sum('total_amount'), 0, '.', ''),
                    number_format($reservation->getTotalAmount(), 0, '.', ''),
                    $reservation->status,
                    $reservation->waiter->name,
                    $reservation->created_at->format('d.m.Y H:i')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function bulkUpdate(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:reservations,id',
            'action' => 'required|in:confirmed,cancelled,pending'
        ]);

        $updated = Reservation::whereIn('id', $request->ids)
            ->update(['status' => $request->action]);

        return response()->json([
            'success' => true,
            'message' => "{$updated} ta rezervatsiya yangilandi"
        ]);
    }

    public function bulkExport(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:reservations,id'
        ]);

        $reservations = Reservation::with(['customer', 'room', 'waiter', 'orders.items.product'])
            ->whereIn('id', $request->ids)
            ->get();

        $filename = 'tanlangan_rezervatsiyalar_' . date('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($reservations) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($file, [
                'Rezervatsiya №',
                'Mijoz',
                'Telefon',
                'Xona',
                'Vaqt',
                'Mehmonlar',
                'Umumiy Summa',
                'Holat'
            ]);

            foreach ($reservations as $reservation) {
                fputcsv($file, [
                    $reservation->reservation_number,
                    $reservation->customer->name,
                    $reservation->customer->phone,
                    $reservation->room->name_uz,
                    $reservation->start_time->format('d.m.Y H:i') . ' - ' . $reservation->end_time->format('H:i'),
                    $reservation->guest_count,
                    number_format($reservation->getTotalAmount(), 0, '.', ''),
                    $reservation->status
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function receipt(Reservation $reservation)
    {
        $reservation->load(['customer', 'room', 'waiter', 'orders.items.product.category']);

        return view('reservations.receipt', compact('reservation'));
    }
}
