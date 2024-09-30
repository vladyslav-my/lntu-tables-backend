<?php

use App\Models\Table;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('booked_tables', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class, 'user_id')->constrained();
            $table->foreignIdFor(User::class, 'guest_id')->constrained('users');
            $table->boolean('user_accepted')->default(true);
            $table->boolean('guest_accepted')->default(false);
            $table->foreignIdFor(Table::class)->constrained();
            $table->dateTime('time_from');
            $table->dateTime('time_to');
            $table->enum('status', ['pending', 'accepted', 'during', 'rejected', 'timeout'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booked_tables');
    }
};
