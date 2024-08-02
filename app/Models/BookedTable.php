<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookedTable extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'guest_id',
        'user_accepted',
        'guest_accepted',
        'table_id',
        'status',
        'time_from',
        'time_to',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function guest()
    {
        return $this->belongsTo(User::class, 'guest_id');
    }

    public function table()
    {
        return $this->belongsTo(Table::class, 'table_id');
    }
}
