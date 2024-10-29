<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ArticleController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'page' => 'required|int',
            'per_page' => 'required|int',
            'keyword' => 'string',
            'older_than' => 'date|before:newer_than',
            'newer_than' => 'date|after:older_than',
            'category' => 'exists:categories,name',
            'source' => 'string'
        ]);

        $query = Article::query();
        if ($request->has('older_than'))
            $query->where('publishedAt', '>=', $request->get('older_than'));

        if ($request->has('newer_than'))
            $query->where('publishedAt', '<=', $request->get('newer_than'));

        if ($request->has('category'))
            $query = Article::whereHas('category', function (Builder $query) use ($request) {
                $query->where('name', '=', $request->get('category'));
            });

        if ($request->has('source'))
            $query->where('publisher', '=', $request->get('source'));

        return response()->json($query->paginate($request->get('per_page')));
    }

    public function detail(string $id)
    {
        return Article::with('category')->findOrFail($id);
    }

    public function newsFeed(Request $request): JsonResponse
    {
        $request->validate([
            'page' => 'required|int',
            'per_page' => 'required|int',
        ]);

        $query = Article::query();
        if (Auth::user()->preference->publisher)
            $query->where('publisher', '=', Auth::user()->preference->publisher);

        if (!empty(Auth::user()->preference->categories))
            $query->orWhereHas('category', function (Builder $query) {
                $query->where(function (Builder $query) {
                    foreach (Auth::user()->preference->categories as $category) {
                        $query->orWhere('name', '=', $category);
                    }
                });
            });

        if (!empty(Auth::user()->preference->authors))
            $query->orWhere(function (Builder $query) {
                foreach (Auth::user()->preference->authors as $author) {
                    $query->orWhere('author', 'LIKE', '%' . $author . '%');
                }
            });

        return response()->json($query->paginate($request->get('per_page')));
    }
}
