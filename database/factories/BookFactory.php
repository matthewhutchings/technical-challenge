<?php

namespace Database\Factories;

use App\Models\Collector;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Book>
 */
class BookFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // Generate a UUID for the book
            'uuid' => (string) Str::uuid(),

            // Generate a random book title
            'title' => $this->faker->sentence(3),

            // Pick a random book type from the given types
            'type' => $this->faker->randomElement(['Fiction', 'Non-Fiction', 'Technical', 'Self-Help']),

            // Associate the book with a random collector
            'collector_id' => Collector::factory(),
        ];
    }
}
