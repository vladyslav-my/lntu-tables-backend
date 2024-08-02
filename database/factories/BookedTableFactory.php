<?php

namespace Database\Factories;

use App\Models\Table;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BookedTable>
 */
class BookedTableFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'guest_id' => User::factory(),
            'table_id' => Table::factory(),
            'time_from' => $this->faker->dateTimeBetween('now'),
            'time_to' => $this->faker->dateTimeBetween('now', '+30 minutes'),
            'user_accepted' => true,
            'guest_accepted' => false,
            'status' => $this->faker->randomElement(['pending', 'accepted', 'rejected', 'timeout']),
        ];
    }
}
