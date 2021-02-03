<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddBook;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $fields = $request->validate([
            'sort.column' => 'in:title,published_year,position',
            'sort.dir' => 'in:asc,desc',
        ]);

        $column = $fields['sort']['column'] ?? 'position';
        $direction = $fields['sort']['dir'] ?? 'desc';

        return $request->user()
            ->books()
            ->orderBy($column, $direction)
            ->paginate();
    }

    /**
     * Store a newly created book in storage.
     *
     * @param AddBook $request
     * @return \Illuminate\Http\Response
     */
    public function store(AddBook $request)
    {
        return $request->user()->books()->create($request->all());
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Book $book
     * @return Book
     */
    public function show(Book $book)
    {
        if (auth()->user()->id !== $book->user_id) {
            abort(Response::HTTP_FORBIDDEN, 'Cannot view this book');
        }

        return $book;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Book $book
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Book $book)
    {
        if (auth()->user()->id !== $book->user_id) {
            abort(Response::HTTP_FORBIDDEN, 'Cannot modify this book');
        }

        if ($request->before) {
            $book2 = $request->user()->books()->findOrFail($request->before);
            $book->moveBefore($book2);
        }
        if ($request->after) {
            $book2 = $request->user()->books()->findOrFail($request->after);
            $book->moveAfter($book2);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Book $book
     * @return \Illuminate\Http\Response
     */
    public function destroy(Book $book)
    {
        if (auth()->user()->id !== $book->user_id) {
            abort(Response::HTTP_FORBIDDEN, 'Cannot modify this book');
        }

        $book->delete();
    }
}
