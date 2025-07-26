<?php

namespace App\Http\Controllers;

use App\Models\HelpArticle;
use Illuminate\Http\Request;

class HelpArticleController extends Controller
{
    /**
     * Display help articles
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        
        $query = HelpArticle::published()->with('author');

        if ($search) {
            $query->search($search);
        }

        $articles = $query->orderBy('published_at', 'desc')->paginate(12);

        // Get popular articles for sidebar
        $popularArticles = HelpArticle::published()
            ->popular(5)
            ->get();

        return view('support.help.index', compact('articles', 'popularArticles', 'search'));
    }

    /**
     * Display a specific help article
     */
    public function show(HelpArticle $article)
    {
        // Check if article is published
        if (!$article->isPublished()) {
            abort(404);
        }

        // Increment view count
        $article->incrementViewCount();

        // Get related articles by tags
        $relatedArticles = HelpArticle::published()
            ->where('id', '!=', $article->id)
            ->when($article->tags, function ($query) use ($article) {
                foreach ($article->tags as $tag) {
                    $query->orWhereJsonContains('tags', $tag);
                }
            })
            ->limit(5)
            ->get();

        return view('support.help.show', compact('article', 'relatedArticles'));
    }

    /**
     * Mark article as helpful
     */
    public function helpful(HelpArticle $article)
    {
        $article->markAsHelpful();

        return response()->json([
            'success' => true,
            'helpful_count' => $article->helpful_count,
            'percentage' => $article->getHelpfulnessPercentage(),
        ]);
    }

    /**
     * Mark article as not helpful
     */
    public function notHelpful(HelpArticle $article)
    {
        $article->markAsNotHelpful();

        return response()->json([
            'success' => true,
            'not_helpful_count' => $article->not_helpful_count,
            'percentage' => $article->getHelpfulnessPercentage(),
        ]);
    }
}
