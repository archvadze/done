<?php

namespace App\Http\Controllers;

use App\Models\Faq;
use App\Models\FaqCategory;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    /**
     * Display FAQ categories and popular questions
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        
        if ($search) {
            $faqs = Faq::active()
                ->search($search)
                ->with('category')
                ->paginate(15);
            
            $categories = collect();
        } else {
            $categories = FaqCategory::active()
                ->ordered()
                ->withCount(['activeFaqs'])
                ->get();
            
            $popularFaqs = Faq::active()
                ->popular(5)
                ->with('category')
                ->get();
                
            $faqs = $popularFaqs;
        }

        return view('support.faq.index', compact('categories', 'faqs', 'search'));
    }

    /**
     * Display FAQs for a specific category
     */
    public function category(FaqCategory $category)
    {
        $faqs = $category->activeFaqs()
            ->ordered()
            ->paginate(20);

        return view('support.faq.category', compact('category', 'faqs'));
    }

    /**
     * Display a specific FAQ
     */
    public function show(Faq $faq)
    {
        // Increment view count
        $faq->incrementViewCount();

        // Get related FAQs from same category
        $relatedFaqs = $faq->category->activeFaqs()
            ->where('id', '!=', $faq->id)
            ->ordered()
            ->limit(5)
            ->get();

        return view('support.faq.show', compact('faq', 'relatedFaqs'));
    }

    /**
     * Mark FAQ as helpful
     */
    public function helpful(Faq $faq)
    {
        $faq->markAsHelpful();

        return response()->json([
            'success' => true,
            'helpful_count' => $faq->helpful_count,
            'percentage' => $faq->getHelpfulnessPercentage(),
        ]);
    }

    /**
     * Mark FAQ as not helpful
     */
    public function notHelpful(Faq $faq)
    {
        $faq->markAsNotHelpful();

        return response()->json([
            'success' => true,
            'not_helpful_count' => $faq->not_helpful_count,
            'percentage' => $faq->getHelpfulnessPercentage(),
        ]);
    }
}
