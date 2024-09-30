<?php

namespace App\Http\Controllers;

use App\Http\Resources\BookedTable\BookedTableResorce;
use App\Mail\BookedTableNotification;
use App\Models\BookedTable;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class BookedTableController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->query('tab', 'current');

        $userId = auth()->user()->id;

        if ($tab === 'my') {
            $bookedTables = BookedTable
                ::where('user_id', $userId)
                ->get();

            return BookedTableResorce::collection($bookedTables);
        } elseif ($tab === 'his') {
            $bookedTables = BookedTable
                ::where('guest_id', $userId)
                ->get();

            return BookedTableResorce::collection($bookedTables);
        } elseif ($tab === 'current') {
            $bookedTables = BookedTable
                ::where('user_id', $userId)
                ->orWhere('guest_id', $userId)
                ->get();

            return BookedTableResorce::collection($bookedTables);
        }

        return response()->json([
            'message' => 'Invalid tab',
        ], 200);
    }


    public function store(Request $request, string $tableId)
    {
        $validator = Validator::make($request->all(), [
            'guest_id' => 'required|integer',
            'data_picker' => 'required|date_format:Y-m-d',
            'time_picker' => 'required|date_format:H:i:s',
            'duration' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        if ($request->guest_id == auth()->user()->id) {
            return response()->json(['error' => 'User ID cannot be the same as Guest ID'], 422);
        }

        $time_from = Carbon::parse("{$request->data_picker} {$request->time_picker}");
        $time_to = $time_from->copy()->addMinutes($request->duration);

        if ($time_from < Carbon::now()) {
            return response()->json(['message' => 'Invalid time'], 422);
        }

        $hasCollision = BookedTable::where('table_id', $tableId)
            ->where(function ($query) use ($time_from, $time_to) {
                $query->where('time_from', '<', $time_to)
                    ->where('time_to', '>', $time_from);
            })
            ->exists();

        if ($hasCollision) {
            return response()->json(['message' => 'This time slot is already booked.'], 422);
        }

        $bookedTable = BookedTable::create([
            'user_id' => auth()->user()->id,
            'guest_id' => $request->guest_id,
            'table_id' => $tableId,
            'time_from' => $time_from,
            'time_to' => $time_to,
            'status' => 'pending',
        ]);

        if ($bookedTable) {
            $guest = User::find($request->guest_id);
            Mail::to($guest->email)->send(new BookedTableNotification($bookedTable));
            

            return response()->json(['message' => 'Table booked successfully'], 201);
        }

        return response()->json(['message' => 'Something went wrong'], 500);
    }


    public function time(Request $request, BookedTable $bookedTable, string $tableId)
    {
        $validator = Validator::make($request->all(), [
            'date_picker' => 'required|date_format:Y-m-d',
        ]);


        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }


        $bookedTables = $bookedTable
            ->where('table_id', $tableId)
            ->whereBetween('time_from', ["{$request->date_picker} 00:00:00}", "{$request->date_picker} 23:59:59"])
            ->get();


        return response()->json($bookedTables->map(fn($bookedTable) => [
            'time_from' => Carbon::parse($bookedTable->time_from)->format('H:i'),
            'time_to' => Carbon::parse($bookedTable->time_to)->format('H:i'),
        ]), 200);
    }


    public function availableTime(Request $request, BookedTable $bookedTable, string $tableId)
    {
        $validator = Validator::make($request->all(), [
            'date_picker' => 'required|date_format:Y-m-d',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $bookedTables = $bookedTable
            ->where('table_id', $tableId)
            ->whereDate('time_from', $request->date_picker)
            ->get();

        $startTime = Carbon::createFromFormat('H:i', '09:00');
        $endTime = Carbon::createFromFormat('H:i', '19:00');
        $timeSlots = [];

        while ($startTime < $endTime) {
            $timeSlots[] = $startTime->copy();
            $startTime->addMinutes(30);
        }

        $availableSlots = [];

        foreach ($timeSlots as $slot) {
            $slotStart = Carbon::createFromFormat('Y-m-d H:i', "{$request->date_picker} {$slot->format('H:i')}");
            $slotEnd = $slotStart->copy()->addMinutes(30);

            $hasCollision = $bookedTables->filter(function ($booked) use ($slotStart, $slotEnd) {
                $bookedStart = Carbon::parse($booked->time_from);
                $bookedEnd = Carbon::parse($booked->time_to);

                return $slotStart->lt($bookedEnd) && $slotEnd->gt($bookedStart);
            })->count();

            if ($slotStart < Carbon::now()) {
                $hasCollision = true;
            }

            if (!$hasCollision) {
                $availableSlots[] = $slotStart->format('H:i');
            }
        }

        if (count($availableSlots) === 0) {
            return response()->json(['message' => 'No available time slots found.'], 404);
        }

        return response()->json($availableSlots);
    }

    public function cancel(string $bookedTableId)
    {
        $bookedTable = BookedTable::where('id', $bookedTableId)
            ->update(['status' => 'rejected', 'user_accepted' => false]);

        if ($bookedTable) {
            return response()->json([
                'message' => 'Table canceled successfully',
            ], 200);
        }

        return response()->json(['message' => "booked table not found"], 404);
    }

    public function decline(string $bookedTableId)
    {
        $bookedTable = BookedTable::where('id', $bookedTableId)
            ->update(['guest_accepted' => false, 'status' => 'rejected']);

        if ($bookedTable) {
            return response()->json([
                'message' => 'Invalitation declined successfully',
            ], 200);
        }

        return response()->json(['message' => "booked table not found"], 404);
    }

    public function accept(string $bookedTableId)
    {
        $bookedTable = BookedTable::where('id', $bookedTableId)
            ->update(['guest_accepted' => true, 'status' => 'accepted']);

        if ($bookedTable) {
            return response()->json([
                'message' => 'Invalitation accepted successfully',
            ], 200);
        }

        return response()->json(['message' => "booked table not found"], 404);
    }
}
