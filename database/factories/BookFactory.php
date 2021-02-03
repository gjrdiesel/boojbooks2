<?php

namespace Database\Factories;

use App\Models\Book;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Book::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'title' => $this->faker->catchPhrase,
            'author' => $this->faker->name,
            'published_date' => $this->faker->date(),
            'rating' => rand(0, 500) / 100, // 250 -> 2.5
            'user_id' => 1
        ];
    }
}
