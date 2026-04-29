<?php

namespace Database\Factories;

use App\Models\Book;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Book>
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
            'title' => $this->faker->sentence(3), 
        'author' => $this->faker->name(),
        'price' => $this->faker->numberBetween(25000, 150000),
        'stock' => $this->faker->numberBetween(0, 100),
        'genre' => $this->faker->randomElement(['Badiiy', 'Tarixiy', 'Ilmiy', 'Siyosiy']),
        'year' => $this->faker->year(),
        'description' => $this->faker->paragraph(),
        'image_url' => 'https://picsum.photos/seed/' . rand(1, 1000) . '/200/300',
        ];
    }
}
