<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Artwork;
use App\Models\Language;
use App\Models\Community;
use App\Models\CommunityMember;
use App\Models\CommunityPost;
use App\Models\Comment;
use App\Models\Evaluation;
use App\Models\ArtworkLike;
use App\Models\Follow;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\FaqCategory;
use App\Models\Faq;
use App\Models\HelpArticle;
use App\Models\SupportTicket;
use App\Models\SupportTicketReply;
use App\Models\ModerationReport;
use App\Models\ModerationAction;
use App\Models\SecurityLog;
use App\Models\Payment;
use App\Models\Withdrawal;
use App\Models\CryptoPayment;
use App\Models\NftOwnership;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ComprehensiveTestDataSeeder extends Seeder
{
    /**
     * Seed the application's database with comprehensive test data.
     */
    public function run(): void
    {
        $this->command->info('Starting comprehensive database seeding...');

        // Step 1: Create Languages (foundation for i18n)
        $this->seedLanguages();

        // Step 2: Create Users with different roles
        $users = $this->seedUsers();

        // Step 3: Create Artworks with various states
        $artworks = $this->seedArtworks($users);

        // Step 4: Create Communities and memberships
        $communities = $this->seedCommunities($users);

        // Step 5: Create Community Posts
        $communityPosts = $this->seedCommunityPosts($users, $communities);

        // Step 6: Create Comments (on artworks and community posts)
        $this->seedComments($users, $artworks, $communityPosts);

        // Step 7: Create Evaluations
        $this->seedEvaluations($users, $artworks);

        // Step 8: Create Social interactions (likes, follows)
        $this->seedSocialInteractions($users, $artworks);

        // Step 9: Create Conversations and Messages
        $this->seedConversations($users);

        // Step 10: Create Support System (FAQs, Help Articles, Tickets)
        $this->seedSupportSystem($users);

        // Step 11: Create Moderation System
        $this->seedModerationSystem($users, $artworks, $communityPosts);

        // Step 12: Create Financial System (Payments, Withdrawals, NFTs)
        $this->seedFinancialSystem($users, $artworks);

        $this->command->info('Comprehensive database seeding completed successfully!');
    }

    private function seedLanguages(): void
    {
        $this->command->info('Seeding languages...');

        $languages = [
            ['code' => 'en', 'name' => 'English', 'native_name' => 'English', 'flag_emoji' => '🇺🇸', 'is_default' => true, 'sort_order' => 1],
            ['code' => 'ka', 'name' => 'Georgian', 'native_name' => 'ქართული', 'flag_emoji' => '🇬🇪', 'is_default' => false, 'sort_order' => 2],
            ['code' => 'es', 'name' => 'Spanish', 'native_name' => 'Español', 'flag_emoji' => '🇪🇸', 'is_default' => false, 'sort_order' => 3],
            ['code' => 'fr', 'name' => 'French', 'native_name' => 'Français', 'flag_emoji' => '🇫🇷', 'is_default' => false, 'sort_order' => 4],
            ['code' => 'de', 'name' => 'German', 'native_name' => 'Deutsch', 'flag_emoji' => '🇩🇪', 'is_default' => false, 'sort_order' => 5],
        ];

        foreach ($languages as $lang) {
            Language::firstOrCreate(['code' => $lang['code']], $lang);
        }
    }

    private function seedUsers(): array
    {
        $this->command->info('Seeding users...');

        $users = [];

        // Create admin user
        $users['admin'] = User::firstOrCreate(['email' => 'admin@artcraft.ge'], [
            'name' => 'System Administrator',
            'email' => 'admin@artcraft.ge',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'email_verified_at' => now(),
            'bio' => 'System administrator managing the ArtCraft platform.',
            'lang' => 'en',
            'status' => 'active',
        ]);

        // Create moderators
        for ($i = 1; $i <= 2; $i++) {
            $users["moderator_{$i}"] = User::firstOrCreate(['email' => "moderator{$i}@artcraft.ge"], [
                'name' => "Moderator User {$i}",
                'email' => "moderator{$i}@artcraft.ge",
                'password' => Hash::make('moderator123'),
                'role' => 'moderator',
                'email_verified_at' => now(),
                'bio' => "Content moderator ensuring quality and safety on the platform.",
                'lang' => $i === 1 ? 'en' : 'ka',
                'status' => 'active',
                'can_evaluate' => true,
            ]);
        }

        // Create artists
        $artistData = [
            ['name' => 'Marina Khvedelidze', 'field' => 'Digital Art', 'lang' => 'ka', 'bio' => 'Contemporary digital artist from Tbilisi specializing in abstract compositions.'],
            ['name' => 'David Rodriguez', 'field' => 'Photography', 'lang' => 'es', 'bio' => 'Street photographer capturing urban life and culture.'],
            ['name' => 'Sophie Laurent', 'field' => 'Painting', 'lang' => 'fr', 'bio' => 'Oil painter inspired by impressionist techniques and modern themes.'],
            ['name' => 'James Mitchell', 'field' => 'Sculpture', 'lang' => 'en', 'bio' => 'Contemporary sculptor working with recycled materials.'],
            ['name' => 'Anna Weber', 'field' => 'Mixed Media', 'lang' => 'de', 'bio' => 'Mixed media artist exploring the intersection of technology and nature.'],
            ['name' => 'Luka Georgiev', 'field' => 'Video Art', 'lang' => 'ka', 'bio' => 'Video artist and filmmaker creating experimental narratives.'],
        ];

        foreach ($artistData as $i => $artist) {
            $email = strtolower(str_replace(' ', '.', $artist['name'])) . '@artcraft.ge';
            $users["artist_{$i}"] = User::firstOrCreate(['email' => $email], [
                'name' => $artist['name'],
                'email' => $email,
                'password' => Hash::make('artist123'),
                'role' => 'artist',
                'email_verified_at' => now(),
                'bio' => $artist['bio'],
                'creative_field' => $artist['field'],
                'lang' => $artist['lang'],
                'status' => 'active',
                'location' => ['Tbilisi, Georgia', 'Barcelona, Spain', 'Paris, France', 'London, UK', 'Berlin, Germany', 'Tbilisi, Georgia'][$i],
                'website' => 'https://' . strtolower(str_replace(' ', '', $artist['name'])) . '.art',
            ]);
        }

        // Create regular users
        for ($i = 1; $i <= 10; $i++) {
            $users["user_{$i}"] = User::firstOrCreate(['email' => "user{$i}@example.com"], [
                'name' => "Regular User {$i}",
                'email' => "user{$i}@example.com",
                'password' => Hash::make('user123'),
                'role' => 'user',
                'email_verified_at' => now(),
                'bio' => "Art enthusiast and platform user #{$i}.",
                'lang' => ['en', 'ka', 'es', 'fr', 'de'][($i - 1) % 5],
                'status' => 'active',
            ]);
        }

        return $users;
    }

    private function seedArtworks(array $users): array
    {
        $this->command->info('Seeding artworks...');

        $artworks = [];
        $categories = ['digital-art', 'painting', 'photography', 'sculpture', 'music', 'video', 'mixed-media'];
        $statuses = ['published', 'published', 'published', 'draft', 'pending']; // More published than others

        $artworkData = [
            ['title' => 'Digital Dreams', 'category' => 'digital-art', 'description' => 'An abstract digital composition exploring the realm of dreams and consciousness.'],
            ['title' => 'Urban Rhythms', 'category' => 'photography', 'description' => 'Street photography capturing the pulse of city life in Barcelona.'],
            ['title' => 'Sunset Reflections', 'category' => 'painting', 'description' => 'Oil painting depicting serene landscape with dramatic lighting.'],
            ['title' => 'Metamorphosis', 'category' => 'sculpture', 'description' => 'Bronze sculpture representing transformation and growth.'],
            ['title' => 'Digital Harmony', 'category' => 'mixed-media', 'description' => 'Mixed media piece combining traditional and digital techniques.'],
            ['title' => 'Time Fragments', 'category' => 'video', 'description' => 'Experimental video art exploring temporal perception.'],
            ['title' => 'Geometric Abstractions', 'category' => 'digital-art', 'description' => 'Series of geometric compositions in vibrant colors.'],
            ['title' => 'Portrait Study #1', 'category' => 'painting', 'description' => 'Contemporary portrait exploring human emotion.'],
            ['title' => 'Night Wanderer', 'category' => 'photography', 'description' => 'Nocturnal urban exploration photography.'],
            ['title' => 'Organic Forms', 'category' => 'sculpture', 'description' => 'Wood sculpture inspired by natural forms.'],
        ];

        $artistUsers = array_filter($users, function($key) {
            return strpos($key, 'artist_') === 0;
        }, ARRAY_FILTER_USE_KEY);

        foreach ($artworkData as $i => $artworkInfo) {
            $artist = array_values($artistUsers)[$i % count($artistUsers)];
            $status = $statuses[$i % count($statuses)];

            $artwork = Artwork::create([
                'user_id' => $artist->id,
                'title' => json_encode(['en' => $artworkInfo['title'], 'ka' => $artworkInfo['title'] . ' (ქართული)']),
                'description' => json_encode(['en' => $artworkInfo['description'], 'ka' => $artworkInfo['description'] . ' ქართული აღწერა.']),
                'media_type' => 'image',
                'file_path' => "artworks/sample_{$i}.jpg",
                'file_url' => "/storage/artworks/sample_{$i}.jpg",
                'thumbnail_path' => "artworks/thumbs/sample_{$i}_thumb.jpg",
                'original_filename' => "artwork_{$i}.jpg",
                'file_hash' => hash('sha256', "artwork_{$i}_" . time()),
                'file_size' => rand(500000, 5000000), // 500KB to 5MB
                'mime_type' => 'image/jpeg',
                'license_type' => ['all_rights_reserved', 'creative_commons_by', 'creative_commons_by_sa'][rand(0, 2)],
                'category' => $artworkInfo['category'],
                'subcategory' => ucfirst($artworkInfo['category']) . ' Art',
                'tags' => json_encode(['abstract', 'contemporary', 'digital', 'art', 'creative']),
                'is_ai_generated' => rand(0, 1) ? true : false,
                'visibility' => 'public',
                'status' => $status,
                'published_at' => $status === 'published' ? now()->subDays(rand(1, 30)) : null,
                'view_count' => rand(10, 1000),
                'like_count' => rand(0, 50),
                'comment_count' => rand(0, 20),
                'evaluation_count' => $status === 'published' ? rand(1, 10) : 0,
                'acq_score' => $status === 'published' ? rand(65, 95) / 10 : null,
                'created_at' => now()->subDays(rand(1, 60)),
            ]);

            $artworks[] = $artwork;
        }

        return $artworks;
    }

    private function seedCommunities(array $users): array
    {
        $this->command->info('Seeding communities...');

        $communities = [];
        $communityData = [
            ['name' => 'Digital Artists Hub', 'slug' => 'digital-artists-hub', 'description' => 'Community for digital artists to share techniques and inspiration.'],
            ['name' => 'Photography Masters', 'slug' => 'photography-masters', 'description' => 'Professional photographers sharing knowledge and critique.'],
            ['name' => 'Contemporary Art Discussion', 'slug' => 'contemporary-art', 'description' => 'Discussing trends and movements in contemporary art.'],
            ['name' => 'Art Critique Circle', 'slug' => 'art-critique-circle', 'description' => 'Constructive feedback and artistic growth community.'],
            ['name' => 'Georgian Artists', 'slug' => 'georgian-artists', 'description' => 'Community for Georgian artists and art enthusiasts.'],
        ];

        $artistUsers = array_filter($users, function($key) {
            return strpos($key, 'artist_') === 0;
        }, ARRAY_FILTER_USE_KEY);

        foreach ($communityData as $i => $communityInfo) {
            $creator = array_values($artistUsers)[$i % count($artistUsers)];

            $community = Community::firstOrCreate(
                ['slug' => $communityInfo['slug']],
                [
                    'name' => $communityInfo['name'],
                    'slug' => $communityInfo['slug'],
                    'description' => $communityInfo['description'],
                    'creator_id' => $creator->id,
                    'privacy' => rand(0, 1) ? 'public' : 'private',
                    'requires_approval' => rand(0, 1),
                    'status' => 'active',
                    'member_count' => rand(5, 50),
                    'created_at' => now()->subDays(rand(30, 180)),
                ]
            );

            // Add creator as member
            CommunityMember::create([
                'community_id' => $community->id,
                'user_id' => $creator->id,
                'role' => 'admin',
                'status' => 'active',
                'joined_at' => $community->created_at,
            ]);

            // Add random members
            $otherUsers = array_filter($users, function($key) use ($creator, $users) {
                return $key !== array_search($creator, $users);
            }, ARRAY_FILTER_USE_KEY);

            $memberCount = rand(5, 15);
            $randomUsers = array_slice(array_values($otherUsers), 0, $memberCount);

            foreach ($randomUsers as $user) {
                CommunityMember::create([
                    'community_id' => $community->id,
                    'user_id' => $user->id,
                    'role' => rand(0, 10) === 0 ? 'moderator' : 'member',
                    'status' => 'active',
                    'joined_at' => now()->subDays(rand(1, 100)),
                ]);
            }

            $communities[] = $community;
        }

        return $communities;
    }

    private function seedCommunityPosts(array $users, array $communities): array
    {
        $this->command->info('Seeding community posts...');

        $communityPosts = [];
        $postTitles = [
            'Tips for Better Digital Art Composition',
            'Critique Request: My Latest Painting',
            'Photography Equipment Recommendations',
            'Thoughts on Modern Art Movements',
            'Georgian Art Heritage Discussion',
            'Color Theory in Practice',
            'Inspiration Sources for Abstract Art',
            'Technical Challenges in Sculpture',
            'Digital vs Traditional Media',
            'Art Market Trends 2025',
        ];

        foreach ($communities as $community) {
            $memberIds = CommunityMember::where('community_id', $community->id)
                ->pluck('user_id')
                ->toArray();

            for ($i = 0; $i < rand(3, 8); $i++) {
                $authorId = $memberIds[array_rand($memberIds)];
                $title = $postTitles[array_rand($postTitles)];

                $post = CommunityPost::create([
                    'community_id' => $community->id,
                    'user_id' => $authorId,
                    'title' => $title,
                    'content' => "This is a detailed discussion post about {$title}. It contains valuable insights and encourages community engagement.",
                    'type' => ['discussion', 'announcement', 'question', 'showcase'][rand(0, 3)],
                    'is_pinned' => rand(0, 10) === 0,
                    'like_count' => rand(0, 25),
                    'comment_count' => rand(0, 15),
                    'created_at' => now()->subDays(rand(1, 30)),
                ]);

                $communityPosts[] = $post;
            }
        }

        return $communityPosts;
    }

    private function seedComments(array $users, array $artworks, array $communityPosts): void
    {
        $this->command->info('Seeding comments...');

        $commentTexts = [
            'Amazing work! The composition is really well balanced.',
            'I love the use of color in this piece.',
            'This reminds me of early impressionist works.',
            'Great technique! How long did this take to create?',
            'The lighting in this piece is exceptional.',
            'Very inspiring! Thanks for sharing.',
            'I would love to see more work in this style.',
            'Constructive feedback: consider adjusting the contrast.',
            'This piece evokes strong emotions.',
            'Brilliant execution of the concept!',
        ];

        // Comments on artworks
        $allUsers = array_values($users);
        foreach ($artworks as $artwork) {
            if ($artwork->status === 'published') {
                $commentCount = rand(1, 8);
                for ($i = 0; $i < $commentCount; $i++) {
                    $commenter = $allUsers[array_rand($allUsers)];
                    
                    // Skip if user is commenting on their own artwork
                    if ($commenter->id === $artwork->user_id) continue;

                    Comment::create([
                        'user_id' => $commenter->id,
                        'commentable_type' => Artwork::class,
                        'commentable_id' => $artwork->id,
                        'artwork_id' => $artwork->id, // Keep for backward compatibility
                        'content' => $commentTexts[array_rand($commentTexts)],
                        'status' => 'active',
                        'created_at' => now()->subDays(rand(1, 20)),
                    ]);
                }
            }
        }

        // Comments on community posts
        $communityCommentTexts = [
            'Great discussion topic! Thanks for bringing this up.',
            'I completely agree with your perspective on this.',
            'This is a very insightful post. Well written!',
            'Could you elaborate more on this point?',
            'I have a different viewpoint on this matter.',
            'Excellent question! Looking forward to the responses.',
            'This really resonates with my own experience.',
            'Thanks for sharing your knowledge with the community.',
            'Very helpful information for beginners like me.',
            'This sparks an interesting debate. What do others think?',
        ];

        foreach ($communityPosts as $post) {
            $commentCount = rand(2, 10); // More comments on community posts
            
            // Get community members to comment
            $communityMembers = \App\Models\CommunityMember::where('community_id', $post->community_id)
                ->where('status', 'active')
                ->pluck('user_id')
                ->toArray();
            
            if (empty($communityMembers)) continue;

            for ($i = 0; $i < $commentCount; $i++) {
                $commenterId = $communityMembers[array_rand($communityMembers)];
                
                // Skip if user is commenting on their own post
                if ($commenterId === $post->user_id) continue;

                // Use the polymorphic relationship to create comments
                $comment = new Comment([
                    'user_id' => $commenterId,
                    'content' => $communityCommentTexts[array_rand($communityCommentTexts)],
                    'status' => 'active'
                ]);
                
                $comment->created_at = $post->created_at->addMinutes(rand(10, 1440));
                $post->comments()->save($comment);
            }

            // Update post comment count
            $post->update(['comment_count' => $post->comments()->count()]);
        }
    }

    private function seedEvaluations(array $users, array $artworks): void
    {
        $this->command->info('Seeding evaluations...');

        $moderators = array_filter($users, function($user) {
            return in_array($user->role, ['moderator', 'admin']);
        });

        foreach ($artworks as $artwork) {
            if ($artwork->status === 'published' && $artwork->evaluation_count > 0) {
                $evaluationCount = min($artwork->evaluation_count, count($moderators));
                
                $selectedModerators = array_slice(array_values($moderators), 0, $evaluationCount);

                foreach ($selectedModerators as $moderator) {
                    $technique = rand(7, 10);
                    $composition = rand(6, 10);
                    $originality = rand(5, 10);
                    $impact = rand(6, 9);
                    
                    $overallScore = ($technique + $composition + $originality + $impact) / 4;

                    Evaluation::create([
                        'artwork_id' => $artwork->id,
                        'evaluator_id' => $moderator->id,
                        'score_technique' => $technique,
                        'score_composition' => $composition,
                        'score_originality' => $originality,
                        'score_impact' => $impact,
                        'overall_score' => round($overallScore, 2),
                        'feedback_text' => 'Professional evaluation with detailed feedback on technical execution and artistic merit.',
                        'source' => 'human',
                        'status' => 'approved',
                        'created_at' => $artwork->published_at->addDays(rand(1, 5)),
                    ]);
                }

                // Update artwork ACQ score based on evaluations
                $avgScore = Evaluation::where('artwork_id', $artwork->id)->avg('overall_score');
                $artwork->update(['acq_score' => round($avgScore, 2)]);
            }
        }
    }

    private function seedSocialInteractions(array $users, array $artworks): void
    {
        $this->command->info('Seeding social interactions...');

        $allUsers = array_values($users);

        // Artwork likes
        foreach ($artworks as $artwork) {
            if ($artwork->status === 'published' && $artwork->like_count > 0) {
                $likerCount = min($artwork->like_count, count($allUsers) - 1);
                $randomUsers = array_slice($allUsers, 0, $likerCount);

                foreach ($randomUsers as $user) {
                    // Skip if user is liking their own artwork
                    if ($user->id === $artwork->user_id) continue;

                    ArtworkLike::create([
                        'artwork_id' => $artwork->id,
                        'user_id' => $user->id,
                        'created_at' => now()->subDays(rand(1, 20)),
                    ]);
                }
            }
        }

        // User follows
        $artistUsers = array_filter($allUsers, function($user) {
            return $user->role === 'artist';
        });

        $regularUsers = array_filter($allUsers, function($user) {
            return $user->role === 'user';
        });

        foreach ($regularUsers as $follower) {
            $followCount = rand(1, 4);
            $artistsToFollow = array_slice(array_values($artistUsers), 0, $followCount);

            foreach ($artistsToFollow as $artist) {
                Follow::create([
                    'follower_id' => $follower->id,
                    'following_id' => $artist->id,
                    'created_at' => now()->subDays(rand(5, 60)),
                ]);
            }
        }
    }

    private function seedConversations(array $users): void
    {
        $this->command->info('Seeding conversations...');

        // Create some private conversations
        $allUsers = array_values($users);
        for ($i = 0; $i < 8; $i++) {
            $participants = array_slice($allUsers, $i * 2, 2);
            
            if (count($participants) < 2) break;

            $conversation = Conversation::create([
                'type' => 'direct',
                'title' => 'Private Chat',
                'created_by' => $participants[0]->id,
                'last_message_at' => now()->subDays(rand(1, 10)),
                'created_at' => now()->subDays(rand(5, 30)),
            ]);

            foreach ($participants as $participant) {
                $conversation->participants()->attach($participant->id, [
                    'joined_at' => $conversation->created_at,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Add messages to conversation
            $messageCount = rand(3, 12);
            for ($j = 0; $j < $messageCount; $j++) {
                $sender = $participants[array_rand($participants)];
                
                Message::create([
                    'conversation_id' => $conversation->id,
                    'user_id' => $sender->id,
                    'content' => "This is message #{$j} in the conversation between users.",
                    'type' => 'text',
                    'created_at' => $conversation->created_at->addMinutes($j * rand(10, 180)),
                ]);
            }
        }
    }

    private function seedSupportSystem(array $users): void
    {
        $this->command->info('Seeding support system...');

        // FAQ Categories
        $faqCategories = [
            ['name' => 'Getting Started', 'description' => 'Basic questions about using the platform'],
            ['name' => 'Account Management', 'description' => 'User account and profile related questions'],
            ['name' => 'Artwork Upload', 'description' => 'Questions about uploading and managing artworks'],
            ['name' => 'Community Features', 'description' => 'Questions about communities and social features'],
            ['name' => 'Technical Issues', 'description' => 'Technical problems and troubleshooting'],
        ];

        foreach ($faqCategories as $catData) {
            $category = FaqCategory::create([
                'name' => $catData['name'],
                'description' => $catData['description'],
                'sort_order' => array_search($catData, $faqCategories) + 1,
            ]);

            // Add FAQs to each category
            for ($i = 1; $i <= rand(3, 6); $i++) {
                Faq::create([
                    'category_id' => $category->id,
                    'question' => "How do I {$catData['name']} - Question {$i}?",
                    'answer' => "This is a detailed answer explaining how to {$catData['name']}. It provides step-by-step instructions and helpful tips.",
                    'sort_order' => $i,
                    'view_count' => rand(10, 500),
                    'helpful_count' => rand(5, 50),
                    'not_helpful_count' => rand(0, 5),
                ]);
            }
        }

        // Help Articles
        $articleTitles = [
            'Complete Guide to Uploading Your First Artwork',
            'Understanding the ACQ Evaluation System',
            'Building Your Artist Profile',
            'Community Guidelines and Best Practices',
            'Privacy Settings and Account Security',
        ];

        $admin = array_values(array_filter($users, function($user) {
            return $user->role === 'admin';
        }))[0];

        foreach ($articleTitles as $title) {
            HelpArticle::create([
                'title' => $title,
                'slug' => Str::slug($title),
                'excerpt' => "Brief overview of {$title}",
                'content' => "# {$title}\n\nThis is a comprehensive help article that explains {$title} in detail. It includes step-by-step instructions, screenshots, and helpful tips.",
                'author_id' => $admin->id,
                'status' => 'published',
                'view_count' => rand(50, 1000),
                'helpful_count' => rand(20, 100),
                'not_helpful_count' => rand(0, 10),
                'published_at' => now()->subDays(rand(30, 90)),
            ]);
        }

        // Support Tickets
        $ticketSubjects = [
            'Unable to upload large artwork files',
            'Account verification issues',
            'Payment processing problems',
            'Community access request',
            'Technical error on profile page',
        ];

        $regularUsers = array_filter($users, function($user) {
            return $user->role === 'user';
        });
        $regularUsersArray = array_values($regularUsers);

        foreach (array_slice($ticketSubjects, 0, 5) as $subject) {
            if (empty($regularUsersArray)) break;
            
            $user = $regularUsersArray[array_rand($regularUsersArray)];
            
            $ticket = SupportTicket::create([
                'user_id' => $user->id,
                'subject' => $subject,
                'description' => "I am experiencing issues with {$subject}. Please help me resolve this problem.",
                'priority' => ['low', 'normal', 'high', 'urgent'][rand(0, 3)],
                'status' => ['open', 'in_progress', 'waiting_for_user', 'resolved', 'closed'][rand(0, 4)],
                'created_at' => now()->subDays(rand(1, 15)),
            ]);

            // Add support replies
            if ($ticket->status !== 'open') {
                $supportUsers = array_filter($users, function($user) {
                    return in_array($user->role, ['admin', 'moderator']);
                });
                $supportUsersArray = array_values($supportUsers);
                
                if (!empty($supportUsersArray)) {
                    $supportUser = $supportUsersArray[array_rand($supportUsersArray)];

                    SupportTicketReply::create([
                        'ticket_id' => $ticket->id,
                        'user_id' => $supportUser->id,
                        'message' => "Thank you for contacting support. We are looking into your issue regarding {$subject}.",
                        'is_internal' => false,
                        'created_at' => $ticket->created_at->addHours(rand(2, 24)),
                    ]);

                    if ($ticket->status === 'resolved') {
                        SupportTicketReply::create([
                            'ticket_id' => $ticket->id,
                            'user_id' => $supportUser->id,
                            'message' => "Your issue has been resolved. Please let us know if you need further assistance.",
                            'is_internal' => false,
                            'created_at' => $ticket->created_at->addDays(rand(1, 3)),
                        ]);
                    }
                }
            }
        }
    }

    private function seedModerationSystem(array $users, array $artworks, array $communityPosts): void
    {
        $this->command->info('Seeding moderation system...');

        $moderators = array_filter($users, function($user) {
            return in_array($user->role, ['moderator', 'admin']);
        });
        $moderatorsArray = array_values($moderators);

        $regularUsers = array_filter($users, function($user) {
            return $user->role === 'user';
        });
        $regularUsersArray = array_values($regularUsers);

        // Moderation Reports
        $reportReasons = ['spam', 'harassment', 'copyright', 'inappropriate_content', 'fake_profile', 'other'];
        
        // Reports on artworks
        foreach (array_slice($artworks, 0, 3) as $artwork) {
            if (empty($regularUsersArray)) break;
            
            $reporter = $regularUsersArray[array_rand($regularUsersArray)];
            
            $report = ModerationReport::create([
                'reporter_id' => $reporter->id,
                'reportable_type' => Artwork::class,
                'reportable_id' => $artwork->id,
                'reason' => $reportReasons[array_rand($reportReasons)],
                'description' => 'This content violates community guidelines.',
                'status' => ['pending', 'under_review', 'resolved', 'dismissed'][rand(0, 3)],
                'created_at' => now()->subDays(rand(1, 10)),
            ]);

            // Add moderation action if report was reviewed
            if (in_array($report->status, ['under_review', 'resolved'])) {
                if (!empty($moderatorsArray)) {
                    $moderator = $moderatorsArray[array_rand($moderatorsArray)];
                    
                    ModerationAction::create([
                        'moderator_id' => $moderator->id,
                        'report_id' => $report->id,
                        'actionable_type' => Artwork::class,
                        'actionable_id' => $artwork->id,
                        'action_type' => ['warning', 'content_removal', 'temporary_ban'][rand(0, 2)],
                        'reason' => 'Content violated community guidelines',
                        'details' => json_encode(['notes' => 'Moderation action taken after review of user report.']),
                        'created_at' => $report->created_at->addHours(rand(2, 48)),
                    ]);
                }
            }
        }

        // Security Logs
        $allUsersArray = array_values($users);
        foreach (array_slice($allUsersArray, 0, 10) as $user) {
            $eventType = ['login', 'logout', 'password_change', 'profile_update'][rand(0, 3)];
            SecurityLog::create([
                'user_id' => $user->id,
                'event_type' => $eventType,
                'event_category' => 'security',
                'description' => "User performed {$eventType} action",
                'ip_address' => '192.168.1.' . rand(1, 254),
                'user_agent' => 'Mozilla/5.0 (compatible test browser)',
                'metadata' => json_encode(['location' => 'Test Location']),
                'severity' => 'info',
                'created_at' => now()->subDays(rand(1, 30)),
            ]);
        }
    }

    private function seedFinancialSystem(array $users, array $artworks): void
    {
        $this->command->info('Seeding financial system...');

        $artistUsers = array_filter($users, function($user) {
            return $user->role === 'artist';
        });

        // Payments
        foreach (array_slice(array_values($artistUsers), 0, 3) as $artist) {
            Payment::create([
                'user_id' => $artist->id,
                'amount' => rand(50, 500),
                'currency' => 'USD',
                'provider' => ['stripe', 'paypal'][rand(0, 1)],
                'payment_id' => 'pay_' . Str::random(16),
                'status' => 'completed',
                'metadata' => json_encode(['description' => 'Artwork sale commission']),
                'created_at' => now()->subDays(rand(5, 60)),
            ]);
        }

        // Withdrawals
        foreach (array_slice(array_values($artistUsers), 0, 2) as $artist) {
            // Update user balance first
            $artist->update(['balance' => rand(100, 1000)]);
            
            Withdrawal::create([
                'user_id' => $artist->id,
                'amount' => rand(50, 300),
                'currency' => 'USD',
                'provider' => ['bank_transfer', 'paypal'][rand(0, 1)],
                'status' => ['pending', 'completed'][rand(0, 1)],
                'payout_details' => json_encode(['account' => 'XXXX-XXXX-1234']),
                'created_at' => now()->subDays(rand(1, 30)),
            ]);
        }

        // Crypto Payments and NFT Ownership
        $allUsersArray = array_values($users);
        foreach (array_slice($artworks, 0, 2) as $artwork) {
            if (rand(0, 1)) {
                // Mark artwork as NFT
                $artwork->update([
                    'is_nft' => true,
                    'nft_contract_address' => '0x' . Str::random(40),
                    'nft_token_id' => rand(1, 10000),
                    'blockchain_network' => 'ethereum',
                    'price' => rand(100, 2000),
                    'is_for_sale' => true,
                ]);

                // Create crypto payment
                if (!empty($allUsersArray)) {
                    $buyer = $allUsersArray[array_rand($allUsersArray)];
                    
                    CryptoPayment::create([
                        'user_id' => $buyer->id,
                        'artwork_id' => $artwork->id,
                        'amount' => $artwork->price,
                        'currency' => 'ETH',
                        'transaction_hash' => '0x' . Str::random(64),
                        'from_address' => '0x' . Str::random(40),
                        'to_address' => '0x' . Str::random(40),
                        'status' => 'confirmed',
                        'network' => 'ethereum',
                        'created_at' => now()->subDays(rand(1, 20)),
                    ]);

                    // Create NFT ownership record
                    NftOwnership::create([
                        'user_id' => $buyer->id,
                        'artwork_id' => $artwork->id,
                        'token_id' => $artwork->nft_token_id,
                        'contract_address' => $artwork->nft_contract_address,
                        'blockchain_network' => $artwork->blockchain_network,
                        'purchase_price' => $artwork->price,
                        'purchase_currency' => 'ETH',
                        'acquired_at' => now()->subDays(rand(1, 20)),
                    ]);
                }
            }
        }
    }
}
