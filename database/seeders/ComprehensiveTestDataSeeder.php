<?php

namespace Database\Seeders;

use App\Models\Faq;
use App\Models\FaqCategory;
use App\Models\HelpArticle;
use App\Models\User;
use Illuminate\Database\Seeder;

class ComprehensiveTestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Starting comprehensive database seeding...');

        // Ensure we have at least one admin user for help articles
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
                'role' => 'admin',
            ]
        );

        // Create FAQ Categories
        $this->command->info('Seeding FAQ categories...');
        $categories = [
            ['name' => 'General Questions', 'slug' => 'general-questions', 'description' => 'Common questions about our platform'],
            ['name' => 'Account & Profile', 'slug' => 'account-profile', 'description' => 'Questions about user accounts and profiles'],
            ['name' => 'Artwork & Submissions', 'slug' => 'artwork-submissions', 'description' => 'Questions about uploading and managing artwork'],
            ['name' => 'Technical Issues', 'slug' => 'technical-issues', 'description' => 'Technical problems and troubleshooting'],
            ['name' => 'Payment & Billing', 'slug' => 'payment-billing', 'description' => 'Questions about payments and billing'],
        ];

        foreach ($categories as $categoryData) {
            FaqCategory::firstOrCreate(
                ['slug' => $categoryData['slug']],
                array_merge($categoryData, [
                    'is_active' => true,
                    'sort_order' => 0,
                ])
            );
        }

        // Create FAQs
        $this->command->info('Seeding FAQs...');
        $faqs = [
            [
                'category_slug' => 'general-questions',
                'question' => 'What is this platform about?',
                'answer' => 'This is a comprehensive platform for artists to showcase their work, get evaluations, and connect with other artists in various communities.',
            ],
            [
                'category_slug' => 'general-questions',
                'question' => 'How do I get started?',
                'answer' => 'Simply create an account, complete your profile, and start exploring communities or uploading your artwork for evaluation.',
            ],
            [
                'category_slug' => 'account-profile',
                'question' => 'How do I update my profile?',
                'answer' => 'Go to your profile page and click the "Edit Profile" button to update your information, avatar, and other details.',
            ],
            [
                'category_slug' => 'account-profile',
                'question' => 'Can I change my username?',
                'answer' => 'Currently, usernames cannot be changed after account creation. Please contact support if you need assistance.',
            ],
            [
                'category_slug' => 'artwork-submissions',
                'question' => 'What file formats are supported?',
                'answer' => 'We support common image formats including JPG, PNG, GIF, and WebP. Maximum file size is 10MB per artwork.',
            ],
            [
                'category_slug' => 'artwork-submissions',
                'question' => 'How do evaluations work?',
                'answer' => 'Other users can evaluate your artwork based on various criteria. You can also evaluate others\' work to contribute to the community.',
            ],
            [
                'category_slug' => 'technical-issues',
                'question' => 'The website is loading slowly. What should I do?',
                'answer' => 'Try clearing your browser cache, checking your internet connection, or try accessing the site from a different browser.',
            ],
            [
                'category_slug' => 'technical-issues',
                'question' => 'I can\'t upload my artwork. What\'s wrong?',
                'answer' => 'Make sure your file is under 10MB and in a supported format (JPG, PNG, GIF, WebP). If the problem persists, contact support.',
            ],
            [
                'category_slug' => 'payment-billing',
                'question' => 'What payment methods do you accept?',
                'answer' => 'We accept major credit cards through Stripe, PayPal, and cryptocurrency payments for premium features.',
            ],
            [
                'category_slug' => 'payment-billing',
                'question' => 'How do I cancel my subscription?',
                'answer' => 'You can cancel your subscription at any time from your account settings. Your access will continue until the end of the billing period.',
            ],
        ];

        foreach ($faqs as $faqData) {
            $category = FaqCategory::where('slug', $faqData['category_slug'])->first();
            if ($category) {
                Faq::firstOrCreate(
                    ['question' => $faqData['question']],
                    [
                        'category_id' => $category->id,
                        'answer' => $faqData['answer'],
                        'is_active' => true,
                        'sort_order' => 0,
                        'view_count' => rand(10, 100),
                        'helpful_count' => rand(5, 50),
                        'not_helpful_count' => rand(0, 5),
                    ]
                );
            }
        }

        // Create Help Articles
        $this->command->info('Seeding help articles...');
        $helpArticles = [
            [
                'title' => 'Getting Started Guide',
                'slug' => 'getting-started-guide',
                'excerpt' => 'A comprehensive guide to help you get started with our platform',
                'content' => '<h2>Welcome to our platform!</h2><p>This guide will walk you through the basic features and help you make the most of your experience.</p><h3>Creating Your Account</h3><p>Start by signing up with your email address...</p>',
                'tags' => ['beginner', 'guide', 'tutorial'],
            ],
            [
                'title' => 'Artwork Upload Best Practices',
                'slug' => 'artwork-upload-best-practices',
                'excerpt' => 'Learn how to properly upload and optimize your artwork for the best experience',
                'content' => '<h2>Uploading Your Artwork</h2><p>Follow these best practices to ensure your artwork looks great and loads quickly.</p><h3>Image Quality</h3><p>Use high-quality images with good resolution...</p>',
                'tags' => ['artwork', 'upload', 'tips'],
            ],
            [
                'title' => 'Community Guidelines',
                'slug' => 'community-guidelines',
                'excerpt' => 'Important guidelines for participating in our communities',
                'content' => '<h2>Community Standards</h2><p>Our community thrives on respect, creativity, and constructive feedback.</p><h3>Respectful Communication</h3><p>Always be respectful in your interactions...</p>',
                'tags' => ['community', 'guidelines', 'rules'],
            ],
            [
                'title' => 'Evaluation System Explained',
                'slug' => 'evaluation-system-explained',
                'excerpt' => 'Understanding how our artwork evaluation system works',
                'content' => '<h2>How Evaluations Work</h2><p>Our evaluation system helps artists improve by providing constructive feedback.</p><h3>Evaluation Criteria</h3><p>Evaluations are based on several criteria...</p>',
                'tags' => ['evaluation', 'feedback', 'system'],
            ],
            [
                'title' => 'Privacy and Security',
                'slug' => 'privacy-and-security',
                'excerpt' => 'Learn about our privacy practices and how to keep your account secure',
                'content' => '<h2>Your Privacy Matters</h2><p>We take your privacy seriously and have implemented robust security measures.</p><h3>Account Security</h3><p>Tips for keeping your account secure...</p>',
                'tags' => ['privacy', 'security', 'safety'],
            ],
        ];

        foreach ($helpArticles as $articleData) {
            HelpArticle::firstOrCreate(
                ['slug' => $articleData['slug']],
                array_merge($articleData, [
                    'author_id' => $adminUser->id,
                    'status' => 'published',
                    'published_at' => now(),
                    'view_count' => rand(50, 500),
                    'helpful_count' => rand(10, 100),
                    'not_helpful_count' => rand(0, 10),
                ])
            );
        }

        $this->command->info('Comprehensive test data seeding completed!');
    }
}
