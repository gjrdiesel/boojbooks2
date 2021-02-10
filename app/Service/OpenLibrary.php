<?php

namespace App\Service;

use Carbon\Carbon;
use Illuminate\Cache\CacheManager;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Http;

class OpenLibrary implements BookApiInterface
{
    protected string $base_url = 'http://openlibrary.org';
    protected Carbon $ttl;
    protected CacheManager $cache;
    private int $per_page;
    private int $current_page;

    public function __construct(CacheManager $cache)
    {
        $this->per_page = 100;
        $this->current_page = intval(request('page', 1));
        $this->ttl = now()->addHour();
        $this->cache = $cache;
    }

    function fetch($method, $endpoint, $args)
    {
        return $this->cache->remember("ol:$method:$endpoint:$this->current_page:" . base64_encode(json_encode($args)), $this->ttl, function () use ($method, $endpoint, $args) {
            return Http::$method("{$this->base_url}/$endpoint.json", $args)->json();
        });
    }

    function search(string $query, $args = [])
    {
        $results = $this->fetch('get', 'search', ['q' => $query, 'page' => $this->current_page] + $args);

        // Modify docs so they're more how we want them for demo's sake
        $items = collect($results['docs'])->map(function ($result, $index) {
            $index += 1;

            return [
                'index' => $index,
                'save_link' => request()->fullUrlWithQuery(['save' => $index]),
                'title' => $result['title'],
                'subtitle' => $result['subtitle'] ?? null,
                'author' => $result['author_name'][0] ?? null,
                'published_year' => $result['publish_year'][0] ?? null,
                'ol_link' => $result['key'],
                'ol_cover' => $result['cover_i'] ?? null,
            ];
        });

        return new LengthAwarePaginator(
            $items, $results['numFound'], $this->per_page, $this->current_page
        );
    }

    public function getLink($key): string
    {
        return $this->base_url . $key;
    }

    public function getCover($key, $size = 'M'): ?string
    {
        if (!$key) {
            return null;
        }

        return "http://covers.openlibrary.org/b/id/$key-$size.jpg";
    }
}
