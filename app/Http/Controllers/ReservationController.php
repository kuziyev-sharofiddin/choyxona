<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\User;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    public function index()
    {
        $reservations = Reservation::with(['customer', 'room', 'waiter'])
            ->orderBy('start_time', 'desc')
            ->paginate(20);

        return view('reservations.index', compact('reservations'));
    }

    public function create(Request $request)
    {
        $rooms = Room::where('status', 'available')->get();
        $waiters = User::whereHas('role', function ($q) {
            $q->where('name', 'waiter');
        })->where('is_active', true)->get();

        $selectedRoom = null;
        if ($request->room_id) {
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
            'start_time' => 'required|date|after:now',
            'end_time' => 'required|date|after:start_time',
            'guest_count' => 'required|integer|min:1',
            'user_id' => 'required|exists:users,id',
        ]);

        // Check room availability
        $room = Room::find($request->room_id);
        if (!$room->isAvailable($request->start_time, $request->end_time)) {
            return back()->withErrors(['room_id' => 'Bu vaqtda xona band!']);
        }

        // Create or find customer
        $customer = Customer::firstOrCreate(
            ['phone' => $request->customer_phone],
            ['name' => $request->customer_name, 'email' => $request->customer_email]
        );

        // Calculate room charge
        $startTime = new \DateTime($request->start_time);
        $endTime = new \DateTime($request->end_time);
        $hours = $endTime->diff($startTime)->h + ($endTime->diff($startTime)->i > 0 ? 1 : 0);
        $roomCharge = $room->hourly_rate * $hours;

        // Create reservation
        $reservation = Reservation::create([
            'customer_id' => $customer->id,
            'room_id' => $request->room_id,
            'user_id' => $request->user_id,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'guest_count' => $request->guest_count,
            'room_charge' => $roomCharge,
            'special_requests' => $request->special_requests,
            'status' => 'confirmed'
        ]);

        // Update room status
        $room->update(['status' => 'occupied']);

        return redirect()->route('reservations.show', $reservation)
            ->with('success', 'Rezervatsiya muvaffaqiyatli yaratildi!');
    }
    public function edit(Reservation $reservation)
    {
        return view('reservations.edit', compact('reservation'));
    }

    public function show(Reservation $reservation)
    {
        $reservation->load(['customer', 'room', 'waiter', 'orders.items.product']);
        return view('reservations.show', compact('reservation'));
    }
    public function update(Request $request, Reservation $reservation)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'room_id' => 'required|exists:rooms,id',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'guest_count' => 'required|integer|min:1',
            'user_id' => 'required|exists:users,id',
            'status' => 'required|in:pending,confirmed,checked_in,completed,cancelled',
            'room_charge' => 'required|numeric|min:0',
        ]);

        // Handle delete request
        if ($request->delete) {
            return $this->destroy($reservation);
        }

        // Update customer
        $reservation->customer->update([
            'name' => $request->customer_name,
            'phone' => $request->customer_phone,
            'email' => $request->customer_email,
        ]);

        // Update reservation
        $reservation->update($request->only([
            'room_id',
            'start_time',
            'end_time',
            'guest_count',
            'user_id',
            'status',
            'room_charge',
            'special_requests'
        ]));

        return redirect()->route('reservations.show', $reservation)
            ->with('success', 'Rezervatsiya muvaffaqiyatli yangilandi!');
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

        // Update customer stats
        $customer = $reservation->customer;
        $customer->increment('visit_count');
        $customer->update(['last_visit' => today()]);

        return back()->with('success', 'Rezervatsiya tugallandi!');
    }
    public function destroy(Reservation $reservation)
    {
        if ($reservation->orders()->count() > 0) {
            return back()->withErrors(['error' => 'Buyurtmalari bo\'lgan rezervatsiyani o\'chirib bo\'lmaydi!']);
        }

        // Update room status if it was occupied
        if ($reservation->room->status === 'occupied') {
            $reservation->room->update(['status' => 'available']);
        }

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
