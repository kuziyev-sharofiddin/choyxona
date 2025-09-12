<?php

// app/Http/Controllers/RoomController.php
namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RoomController extends Controller
{
    public function index()
    {
        $rooms = Room::withCount(['reservations' => function($q) {
            $q->whereDate('start_time', today());
        }])->orderBy('name')->get();

        return view('rooms.index', compact('rooms'));
    }

    public function create()
    {
        return view('rooms.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'name_uz' => 'required|string|max:255',
            'capacity' => 'required|integer|min:1',
            'hourly_rate' => 'required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'amenities' => 'nullable|array',
        ]);

        $data = $request->all();
        
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('rooms', 'public');
        }

        Room::create($data);

        return redirect()->route('rooms.index')
                        ->with('success', 'Xona muvaffaqiyatli qo\'shildi!');
    }

    public function show(Room $room)
    {
        $room->load(['reservations' => function($q) {
            $q->with('customer')->orderBy('start_time', 'desc')->limit(10);
        }]);
        
        $currentReservation = $room->currentReservation;
        $todayReservations = $room->reservations()
                                 ->whereDate('start_time', today())
                                 ->with('customer')
                                 ->orderBy('start_time')
                                 ->get();

        return view('rooms.show', compact('room', 'currentReservation', 'todayReservations'));
    }

    public function edit(Room $room)
    {
        return view('rooms.edit', compact('room'));
    }

    public function update(Request $request, Room $room)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'name_uz' => 'required|string|max:255',
            'capacity' => 'required|integer|min:1',
            'hourly_rate' => 'required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'amenities' => 'nullable|array',
        ]);

        $data = $request->all();
        
        if ($request->hasFile('image')) {
            if ($room->image) {
                Storage::disk('public')->delete($room->image);
            }
            $data['image'] = $request->file('image')->store('rooms', 'public');
        }

        $room->update($data);

        return redirect()->route('rooms.index')
                        ->with('success', 'Xona muvaffaqiyatli yangilandi!');
    }

    public function destroy(Room $room)
    {
        if ($room->reservations()->count() > 0) {
            return back()->withErrors(['error' => 'Ushbu xonada rezervatsiyalar mavjud, o\'chirib bo\'lmaydi!']);
        }

        if ($room->image) {
            Storage::disk('public')->delete($room->image);
        }

        $room->delete();

        return redirect()->route('rooms.index')
                        ->with('success', 'Xona o\'chirildi!');
    }

    public function setMaintenance(Room $room)
    {
        $room->update(['status' => $room->status === 'maintenance' ? 'available' : 'maintenance']);
        
        $status = $room->status === 'maintenance' ? 'ta\'mir holatiga' : 'mavjud holatiga';
        return response()->json(['message' => "Xona {$status} o'tkazildi!"]);
    }

    public function checkAvailability(Request $request, Room $room)
    {
        $startTime = $request->start_time;
        $endTime = $request->end_time;
        
        $isAvailable = $room->isAvailable($startTime, $endTime);
        
        return response()->json(['available' => $isAvailable]);
    }

    public function getAvailableRooms(Request $request)
    {
        $startTime = $request->start_time;
        $endTime = $request->end_time;
        
        $rooms = Room::where('status', 'available')
                    ->get()
                    ->filter(function($room) use ($startTime, $endTime) {
                        return $room->isAvailable($startTime, $endTime);
                    })
                    ->values();
        
        return response()->json($rooms);
    }
}