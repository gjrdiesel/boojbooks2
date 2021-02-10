<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\User;
use App\Service\BookApiInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Pagination\LengthAwarePaginator;
use Tests\TestCase;

class HandleListOfBooksTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_add_book()
    {
        app()->bind(BookApiInterface::class, function () {
            return new class implements BookApiInterface {
                function search(string $query, $args = [])
                {
                    return new LengthAwarePaginator(
                        [
                            ['title' => 'The Test Gatsby'],
                            [
                                'title' => 'The Great Gatsby',
                                'save_link' => request()->fullUrlWithQuery(['save' => 2]),
                                'subtitle' => 'subtitle',
                                'author' => 'author',
                                'published_year' => 2021,
                                'ol_link' => 'ol_link',
                                'ol_cover' => 'ol_cover',
                            ],
                        ], 2, 10, 1
                    );
                }
            };
        });

        $this->withoutExceptionHandling();

        $user = User::factory()->create();
        $this->actingAs($user);

        $books = $this->get('/search?query=the+great+gatsby&page=1')->json('data');
        $this->get($books[1]['save_link'])->json('data');

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

    public function test_can_sort_books_by_year()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Book::factory()->create(['title' => 'Book 1', 'published_year' => 2003, 'user_id' => $user->id]);
        Book::factory()->create(['title' => 'Book 2', 'published_year' => 2001, 'user_id' => $user->id]);
        Book::factory()->create(['title' => 'Book 3', 'published_year' => 2002, 'user_id' => $user->id]);

        $books = $this->getJson('/book?sort[column]=published_year')->json('data');

        $this->assertEquals(['Book 1', 'Book 3', 'Book 2'], \Arr::pluck($books, 'title'));
    }

    public function test_sorts_books_by_position_by_default()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Book::factory()->create(['title' => 'Book 1', 'position' => 3, 'user_id' => $user->id]);
        Book::factory()->create(['title' => 'Book 2', 'position' => 1, 'user_id' => $user->id]);
        Book::factory()->create(['title' => 'Book 3', 'position' => 2, 'user_id' => $user->id]);

        $books = $this->getJson('/book')->json('data');

        $this->assertEquals(['Book 1', 'Book 3', 'Book 2'], \Arr::pluck($books, 'title'));
    }

    public function test_get_3_book_details()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $book = Book::factory()->create(['user_id' => $user->id]);

        $books = $this->getJson("/book/{$book->id}")->json();

        $this->assertNotEmpty($books['title']);
        $this->assertNotEmpty($books['author']);
        $this->assertNotEmpty($books['published_year']);
    }
}
