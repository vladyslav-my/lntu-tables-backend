<?php

namespace App\Http\Controllers;

use App\Http\Resources\BookedTable\BookedTableResorce;
use App\Models\BookedTable;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
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

        $time_from = Carbon::parse("{$request->data_picker} {$request->time_picker}");
        $time_to = $time_from->copy()->addMinutes($request->duration);

        $bookedTable = BookedTable::create([
            'user_id' => auth()->user()->id,
            'guest_id' => $request->guest_id,
            'table_id' => $tableId,
            'time_from' => $time_from,
            'time_to' => $time_to,
            'status' => 'pending',
        ]);


        if ($bookedTable) {
            return response()->json([
                'message' => 'Table booked successfully',
            ], 201);
        }
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
            ->whereBetween('time_from', [$request->date_picker.' 00:00:00', $request->date_picker.' 23:59:59'])
            ->get();

        
        return response()->json($bookedTables->map(fn ($bookedTable) => [
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

    // Получаем все забронированные столики на выбранную дату
    $bookedTables = $bookedTable
        ->where('table_id', $tableId)
        ->whereDate('time_from', $request->date_picker)
        ->get();
        
    // Генерируем временные интервалы с 9:00 до 19:00 с шагом в 30 минут
    $startTime = Carbon::createFromFormat('H:i', '09:00');
    $endTime = Carbon::createFromFormat('H:i', '19:00');
    $timeSlots = [];

    while ($startTime < $endTime) {
        $timeSlots[] = $startTime->copy(); // Клонируем объект, чтобы не изменять основной объект
        $startTime->addMinutes(30);
    }

    // Проверяем каждый временной интервал на наличие коллизий
    $availableSlots = [];

    foreach ($timeSlots as $slot) {
        $slotStart = Carbon::createFromFormat('Y-m-d H:i', $request->date_picker . ' ' . $slot->format('H:i'));
        $slotEnd = $slotStart->copy()->addMinutes(30);

        // Проверяем коллизии с уже забронированными временными интервалами
        $hasCollision = $bookedTables->filter(function ($booked) use ($slotStart, $slotEnd) {
            $bookedStart = Carbon::parse($booked->time_from);
            $bookedEnd = Carbon::parse($booked->time_to);

            // Проверяем перетин интервалов (исключаем конец интервала)
            return $slotStart->lt($bookedEnd) && $slotEnd->gt($bookedStart);
        })->count() > 0;

        // Если коллизий нет, добавляем этот временной интервал в доступные
        if (!$hasCollision) {
            $availableSlots[] = $slotStart->format('H:i');
        }
    }

    // Возвращаем доступные временные интервалы
    return response()->json($availableSlots);
}




    public function cancel($userId)
    {
        $user = auth()->user();
        $bookedTable = BookedTable::find($userId)->where('user_id', $user->id)->update(['status' => 'rejected']);

        if ($bookedTable) {
            return response()->json([
                'message' => 'Table canceled successfully',
            ], 200);
        }
    }


    public function decline($userId)
    {
        $user = auth()->user();
        $bookedTable = BookedTable::find($userId)->where('guest_id', $user->id)->update(['guest_accepted' => false]);

        if ($bookedTable) {
            return response()->json([
                'message' => 'Invalitation declined successfully',
            ], 200);
        }
    }


    public function accept($userId)
    {
        $user = auth()->user();
        $bookedTable = BookedTable::find($userId)->where('guest_id', $user->id)->update(['guest_accepted' => true]);

        if ($bookedTable) {
            return response()->json([
                'message' => 'Invalitation accepted successfully',
            ], 200);
        }
    }
}