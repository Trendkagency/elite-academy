<?php

namespace App\Http\Controllers\Blog;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BlogController extends Controller
{
    public function index(Request $request): View
    {
        $selectedCategory = $request->query('category');

        $articles = Article::where('is_published', true)
            ->when($selectedCategory, function ($query) use ($selectedCategory) {
                $query->where('category', 'like', "%{$selectedCategory}%");
            })
            ->with('authorUser')
            ->orderBy('published_at', 'desc')
            ->paginate(5)
            ->withQueryString();

        $categories = ['Programming', 'AI & Tech', 'Study Tips', 'Announcements', 'Mathematics', 'Science'];

        return view('pages.blog', [
            'pageTitle' => 'Blog — Elite Academy',
            'activeNav' => 'blog',
            'articles' => $articles,
            'categories' => $categories,
            'selectedCategory' => $selectedCategory,
        ]);
    }

    public function show(?string $slug = null): View
    {
        $article = Article::where('is_published', true)
            ->when($slug, fn ($query) => $query->where('slug', $slug))
            ->with('authorUser')
            ->first();

        $relatedArticles = Article::where('is_published', true)
            ->when($article, fn ($q) => $q->where('id', '!=', $article->id))
            ->limit(3)
            ->get();

        return view('pages.blog-details', [
            'pageTitle' => $article ? $article->title : 'Blog Details — Elite Academy',
            'activeNav' => 'blog',
            'article' => $article,
            'relatedArticles' => $relatedArticles,
        ]);
    }
}
