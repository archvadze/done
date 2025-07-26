<?php

namespace App\Http\Controllers;

use App\Models\Faq;
use App\Models\FaqCategory;
use App\Models\HelpArticle;
use App\Models\SupportTicket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class SupportController extends Controller
{
    /**
     * Display support center dashboard
     */
    public function index()
    {
        // Get popular FAQs
        $popularFaqs = Faq::active()->popular(6)->with('category')->get();
        
        // Get recent help articles
        $recentArticles = HelpArticle::published()->recent(4)->with('author')->get();
        
        // Get FAQ categories
        $faqCategories = FaqCategory::active()->ordered()->withCount(['activeFaqs'])->limit(6)->get();
        
        // Get user's recent tickets if authenticated
        $recentTickets = Auth::check() 
            ? Auth::user()->supportTickets()->latest()->limit(3)->get()
            : collect();

        return view('support.index', compact('popularFaqs', 'recentArticles', 'faqCategories', 'recentTickets'));
    }

    /**
     * Show contact form
     */
    public function contact()
    {
        return view('support.contact');
    }

    /**
     * Process contact form submission
     */
    public function submitContact(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:5000',
            'category' => 'required|in:general,technical,billing,partnership,media',
        ]);

        // If user is authenticated, create a support ticket instead
        if (Auth::check()) {
            $ticket = SupportTicket::create([
                'user_id' => Auth::id(),
                'subject' => $validated['subject'],
                'description' => $validated['message'],
                'category' => $this->mapContactCategoryToTicketCategory($validated['category']),
                'priority' => 'normal',
            ]);

            return redirect()->route('support.tickets.show', $ticket)
                ->with('success', 'Your message has been converted to a support ticket: #' . $ticket->ticket_number);
        }

        // For guests, send email (you would implement email sending here)
        // Mail::to(config('mail.support_email'))->send(new ContactFormMail($validated));

        return back()->with('success', 'Thank you for your message! We will get back to you soon.');
    }

    /**
     * Search across all support content
     */
    public function search(Request $request)
    {
        $search = $request->get('q', '');
        
        if (empty($search)) {
            return redirect()->route('support.index');
        }

        // Search FAQs
        $faqs = Faq::active()
            ->search($search)
            ->with('category')
            ->limit(10)
            ->get();

        // Search Help Articles
        $articles = HelpArticle::published()
            ->search($search)
            ->with('author')
            ->limit(10)
            ->get();

        return view('support.search', compact('faqs', 'articles', 'search'));
    }

    /**
     * Map contact category to ticket category
     */
    private function mapContactCategoryToTicketCategory(string $category): string
    {
        return match($category) {
            'technical' => 'technical',
            'billing' => 'billing',
            'general', 'partnership', 'media' => 'other',
            default => 'other',
        };
    }
}
