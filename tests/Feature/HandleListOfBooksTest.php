<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;

class HandleListOfBooksTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_add_book()
    {
        $this->withoutExceptionHandling();

        $user = User::factory()->create();
        $this->actingAs($user);

        $this->post('/book', [
            'title' => 'The Great Gatsby',
            'author' => 'F. Scott Fitzgerald',
            'published_date' => '1925-04-10'
        ])->assertStatus(Response::HTTP_CREATED);

        $this->assertDatabaseHas('books', ['title' => 'The Great Gatsby', 'user_id' => $user->id]);
    }

    public function test_can_remove_book()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $book = Book::factory()->create(['user_id' => $user->id]);

        $this->delete("/book/{$book->id}");

        $this->assertDatabaseMissing('books', ['title' => $book->title]);
    }

    public function test_can_change_book_order()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $book1 = Book::factory()->create(['title' => 'Book 1', 'user_id' => $user->id]);
        $book2 = Book::factory()->create(['title' => 'Book 2', 'user_id' => $user->id]);
        $book3 = Book::factory()->create(['title' => 'Book 3', 'user_id' => $user->id]);

        $this->assertEquals(['Book 1', 'Book 2', 'Book 3'], Book::sorted()->get()->pluck('title')->toArray());

        // Move before
        $this->patch("/book/{$book3->id}", [
            'before' => $book1->id
        ]);
        $this->assertEquals(['Book 3', 'Book 1', 'Book 2'], Book::sorted()->get()->pluck('title')->toArray());

        // Move after
        $this->patch("/book/{$book3->id}", [
            'after' => $book2->id
        ]);
        $this->assertEquals(['Book 1', 'Book 2', 'Book 3'], Book::sorted()->get()->pluck('title')->toArray());
    }

    public function test_can_sort_books_by_rating()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Book::factory()->create(['title' => 'Book 1', 'rating' => 3, 'user_id' => $user->id]);
        Book::factory()->create(['title' => 'Book 2', 'rating' => 1, 'user_id' => $user->id]);
        Book::factory()->create(['title' => 'Book 3', 'rating' => 2, 'user_id' => $user->id]);

        $books = $this->getJson('/book?sort[column]=rating')->json('data');

        $this->assertEquals(['Book 1', 'Book 3', 'Book 2'], \Arr::pluck($books, 'title'));
    }

    public function test_get_3_book_details()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $book1 = Book::factory()->create(['user_id' => $user->id]);

        $books = $this->getJson('/books')->json();

        $this->assertNotNull($books[0]->title);
        $this->assertNotNull($books[0]->rating);
        $this->assertNotNull($books[0]->published_date);
    }
}
