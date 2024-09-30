<?php

namespace App\Http\Resources\BookedTable;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


class BookedTableResorce extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'date_start' => Carbon::parse($this->time_from)->toDateString(),
            'time_from' => Carbon::parse($this->time_from)->toTimeString(),
            'time_to' => Carbon::parse($this->time_to)->toTimeString(),
            'table' => [
                "id" => $this->table_id,
                "number" => $this->table->number
            ],
            'user' => [
                "id" => $this->user_id,
                "name" => $this->user->name,
                "last_name" => $this->user->last_name,
                "image" => null,
                "role" => $this->user->role
            ],
            'guest' => [
                "id" => $this->guest_id,
                "name" => $this->guest->name,
                "last_name" => $this->guest->last_name,
                "image" => null,
                "role" => $this->guest->role
            ],
            'user_accepted' => (bool) $this->user_accepted,
            'guest_accepted' => (bool) $this->guest_accepted,
            'status' => $this->status,
        ];
    }
}
