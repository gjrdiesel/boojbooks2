<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Service\BookApiInterface;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    private BookApiInterface $api;

    public function __construct(BookApiInterface $api)
    {
        $this->api = $api;
    }

    /**
     * Handle the incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $fields = $request->validate([
            'query' => 'required|string|min:3',
            'save' => 'nullable|integer',
        ]);

        if ($fields['save'] ?? false) {
            $this->saveBook($fields['save'] - 1, $fields['query'], $request->page, $request->user());
        }

        return $this->api->search($fields['query']);
    }

    private function saveBook($id, $query, $page, User $user)
    {
        $book = $this->api->search($query)[$id] ?? null;

        if (!$book) {
            abort(500, "Could not find book $id, page $page");
        }

        return $user->books()->create([
            'title' => $book['title'],
            'subtitle' => $book['subtitle'],
            'author' => $book['author'],
            'published_year' => $book['published_year'],
            'ol_link' => $book['ol_link'],
            'ol_cover' => $book['ol_cover'],
        ]);
    }
}
