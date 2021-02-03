<?php

namespace App\Http\Controllers;

use App\Models\User;
use Facades\App\Service\OpenLibrary;
use Illuminate\Http\Request;

class SearchController extends Controller
{
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
            $this->saveBook($fields['save'], $fields['query'], $request->page, $request->user());
        }

        return OpenLibrary::search($fields['query']);
    }

    private function saveBook($id, $query, $page, User $user)
    {
        $book = OpenLibrary::search($query)[$id] ?? null;

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
