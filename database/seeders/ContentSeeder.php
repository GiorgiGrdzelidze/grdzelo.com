<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\Certification;
use App\Models\Education;
use App\Models\Experience;
use App\Models\Hobby;
use App\Models\Page;
use App\Models\Project;
use App\Models\Service;
use App\Models\Skill;
use App\Models\SocialLink;
use App\Models\Testimonial;
use App\Models\User;
use Illuminate\Database\Seeder;

class ContentSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();

        // Pages
        $pages = [
            ['title' => 'Home', 'slug' => 'home', 'template' => 'home', 'status' => 'published'],
            ['title' => 'About', 'slug' => 'about', 'template' => 'about', 'status' => 'published'],
            ['title' => 'Contact', 'slug' => 'contact', 'template' => 'contact', 'status' => 'published'],
            ['title' => 'Privacy Policy', 'slug' => 'privacy-policy', 'template' => 'default', 'status' => 'published'],
        ];

        foreach ($pages as $page) {
            Page::create($page);
        }

        // Services
        $services = [
            [
                'title' => 'Full-Stack Web Development',
                'slug' => 'full-stack-web-development',
                'summary' => 'End-to-end development of modern web applications using Laravel, Vue.js, and industry-leading tools. From database architecture to pixel-perfect frontends.',
                'icon' => 'code-2',
                'is_featured' => true,
                'sort_order' => 1,
            ],
            [
                'title' => 'API Design & Development',
                'slug' => 'api-design-development',
                'summary' => 'RESTful and GraphQL API design with proper authentication, rate limiting, documentation, and versioning strategies.',
                'icon' => 'webhook',
                'is_featured' => true,
                'sort_order' => 2,
            ],
            [
                'title' => 'Technical Architecture',
                'slug' => 'technical-architecture',
                'summary' => 'System design, database modeling, infrastructure planning, and technical decision-making for scalable applications.',
                'icon' => 'layout-dashboard',
                'is_featured' => true,
                'sort_order' => 3,
            ],
            [
                'title' => 'Performance Optimization',
                'slug' => 'performance-optimization',
                'summary' => 'Audit and optimize application performance — database queries, caching strategies, frontend bundle size, and Core Web Vitals.',
                'icon' => 'gauge',
                'is_featured' => true,
                'sort_order' => 4,
            ],
            [
                'title' => 'Code Review & Mentoring',
                'slug' => 'code-review-mentoring',
                'summary' => 'In-depth code reviews, architectural guidance, and mentoring for development teams looking to level up.',
                'icon' => 'users',
                'is_featured' => false,
                'sort_order' => 5,
            ],
            [
                'title' => 'CMS & Admin Panel Development',
                'slug' => 'cms-admin-panel-development',
                'summary' => 'Custom content management systems and admin panels built with Filament, tailored to your exact workflow.',
                'icon' => 'panel-left',
                'is_featured' => true,
                'sort_order' => 6,
            ],
        ];

        foreach ($services as $service) {
            Service::create($service);
        }

        // Skills
        $skillsData = [
            ['name' => 'PHP', 'slug' => 'php', 'category' => 'Backend', 'proficiency_label' => 'Expert', 'proficiency_score' => 95, 'years_experience' => 8, 'is_featured' => true, 'is_visible' => true, 'sort_order' => 1],
            ['name' => 'Laravel', 'slug' => 'laravel', 'category' => 'Backend', 'proficiency_label' => 'Expert', 'proficiency_score' => 95, 'years_experience' => 7, 'is_featured' => true, 'is_visible' => true, 'sort_order' => 2],
            ['name' => 'Vue.js', 'slug' => 'vuejs', 'category' => 'Frontend', 'proficiency_label' => 'Expert', 'proficiency_score' => 90, 'years_experience' => 6, 'is_featured' => true, 'is_visible' => true, 'sort_order' => 3],
            ['name' => 'TypeScript', 'slug' => 'typescript', 'category' => 'Frontend', 'proficiency_label' => 'Advanced', 'proficiency_score' => 85, 'years_experience' => 4, 'is_featured' => true, 'is_visible' => true, 'sort_order' => 4],
            ['name' => 'Tailwind CSS', 'slug' => 'tailwind-css', 'category' => 'Frontend', 'proficiency_label' => 'Expert', 'proficiency_score' => 92, 'years_experience' => 5, 'is_featured' => true, 'is_visible' => true, 'sort_order' => 5],
            ['name' => 'MySQL', 'slug' => 'mysql', 'category' => 'Database', 'proficiency_label' => 'Advanced', 'proficiency_score' => 88, 'years_experience' => 7, 'is_featured' => true, 'is_visible' => true, 'sort_order' => 6],
            ['name' => 'PostgreSQL', 'slug' => 'postgresql', 'category' => 'Database', 'proficiency_label' => 'Advanced', 'proficiency_score' => 82, 'years_experience' => 4, 'is_featured' => false, 'is_visible' => true, 'sort_order' => 7],
            ['name' => 'Redis', 'slug' => 'redis', 'category' => 'Database', 'proficiency_label' => 'Advanced', 'proficiency_score' => 80, 'years_experience' => 5, 'is_featured' => true, 'is_visible' => true, 'sort_order' => 8],
            ['name' => 'Docker', 'slug' => 'docker', 'category' => 'DevOps', 'proficiency_label' => 'Advanced', 'proficiency_score' => 82, 'years_experience' => 4, 'is_featured' => true, 'is_visible' => true, 'sort_order' => 9],
            ['name' => 'Git', 'slug' => 'git', 'category' => 'Tools', 'proficiency_label' => 'Expert', 'proficiency_score' => 93, 'years_experience' => 8, 'is_featured' => false, 'is_visible' => true, 'sort_order' => 10],
            ['name' => 'Inertia.js', 'slug' => 'inertiajs', 'category' => 'Frontend', 'proficiency_label' => 'Expert', 'proficiency_score' => 90, 'years_experience' => 4, 'is_featured' => true, 'is_visible' => true, 'sort_order' => 11],
            ['name' => 'Filament', 'slug' => 'filament', 'category' => 'Backend', 'proficiency_label' => 'Expert', 'proficiency_score' => 90, 'years_experience' => 3, 'is_featured' => true, 'is_visible' => true, 'sort_order' => 12],
            ['name' => 'REST APIs', 'slug' => 'rest-apis', 'category' => 'Backend', 'proficiency_label' => 'Expert', 'proficiency_score' => 92, 'years_experience' => 7, 'is_featured' => false, 'is_visible' => true, 'sort_order' => 13],
            ['name' => 'Linux', 'slug' => 'linux', 'category' => 'DevOps', 'proficiency_label' => 'Advanced', 'proficiency_score' => 78, 'years_experience' => 6, 'is_featured' => false, 'is_visible' => true, 'sort_order' => 14],
            ['name' => 'Vite', 'slug' => 'vite', 'category' => 'Tools', 'proficiency_label' => 'Advanced', 'proficiency_score' => 85, 'years_experience' => 3, 'is_featured' => false, 'is_visible' => true, 'sort_order' => 15],
        ];

        $skills = [];
        foreach ($skillsData as $data) {
            $skills[$data['slug']] = Skill::create($data);
        }

        // Experiences
        $experiences = [
            [
                'company' => 'Freelance',
                'role' => 'Senior Full-Stack Developer',
                'type' => 'freelance',
                'start_date' => '2022-01-01',
                'end_date' => null,
                'is_current' => true,
                'summary' => 'Building custom web applications, CMS platforms, and admin panels for clients across various industries. Specializing in Laravel + Vue.js full-stack solutions with Filament admin.',
                'achievements' => ['Delivered 15+ production applications', 'Maintained 100% client satisfaction rate', 'Implemented SSR and SEO-first architecture patterns'],
                'technologies' => ['Laravel', 'Vue.js', 'Filament', 'Tailwind CSS', 'MySQL', 'Docker'],
                'sort_order' => 1,
            ],
            [
                'company' => 'TechCorp',
                'role' => 'Full-Stack Developer',
                'type' => 'job',
                'start_date' => '2019-06-01',
                'end_date' => '2021-12-31',
                'is_current' => false,
                'summary' => 'Led development of internal tools and client-facing web applications. Introduced modern frontend practices with Vue.js and established CI/CD pipelines.',
                'achievements' => ['Reduced page load times by 60%', 'Led migration from jQuery to Vue.js', 'Built real-time dashboard serving 5K+ daily users'],
                'technologies' => ['PHP', 'Laravel', 'Vue.js', 'MySQL', 'Redis', 'AWS'],
                'sort_order' => 2,
            ],
            [
                'company' => 'StartupHub',
                'role' => 'Junior Developer',
                'type' => 'job',
                'start_date' => '2017-03-01',
                'end_date' => '2019-05-31',
                'is_current' => false,
                'summary' => 'Developed and maintained multiple web applications. Grew from junior to mid-level, taking on more responsibility with each project.',
                'achievements' => ['Built 3 client applications from scratch', 'Implemented automated testing workflows', 'Mentored 2 interns'],
                'technologies' => ['PHP', 'Laravel', 'JavaScript', 'MySQL', 'Bootstrap'],
                'sort_order' => 3,
            ],
        ];

        foreach ($experiences as $exp) {
            Experience::create($exp);
        }

        // Testimonials
        $testimonials = [
            [
                'author_name' => 'Sarah Johnson',
                'author_role' => 'CTO',
                'company' => 'GreenLeaf Tech',
                'quote' => 'Exceptional developer who truly understands the intersection of engineering and product. The application he built for us exceeded our expectations in both quality and performance.',
                'rating' => 5,
                'is_featured' => true,
                'is_visible' => true,
                'sort_order' => 1,
            ],
            [
                'author_name' => 'Michael Chen',
                'author_role' => 'Product Manager',
                'company' => 'Nexus Digital',
                'quote' => 'One of the most reliable engineers I\'ve worked with. His attention to detail and ability to ship clean, maintainable code is impressive. Would recommend without hesitation.',
                'rating' => 5,
                'is_featured' => true,
                'is_visible' => true,
                'sort_order' => 2,
            ],
            [
                'author_name' => 'Emily Rodriguez',
                'author_role' => 'Founder',
                'company' => 'Artisan Marketplace',
                'quote' => 'He built our entire e-commerce platform from the ground up. The admin panel is intuitive, the frontend is beautiful, and the whole system runs like clockwork.',
                'rating' => 5,
                'is_featured' => true,
                'is_visible' => true,
                'sort_order' => 3,
            ],
        ];

        foreach ($testimonials as $t) {
            Testimonial::create($t);
        }

        // Projects
        $projects = [
            [
                'title' => 'E-Commerce Platform',
                'slug' => 'e-commerce-platform',
                'summary' => 'A full-featured e-commerce platform with real-time inventory, multi-vendor support, and an advanced admin dashboard.',
                'description' => '<p>Built a comprehensive e-commerce solution handling thousands of daily transactions. Features include real-time inventory management, multi-vendor marketplace capabilities, and a powerful Filament-based admin panel.</p>',
                'challenge' => 'The client needed a platform that could handle high-concurrency during flash sales while maintaining sub-second response times.',
                'solution' => 'Implemented queue-based order processing, Redis caching layers, and database query optimization. Used Laravel Octane for improved throughput.',
                'tech_stack' => ['Laravel', 'Vue.js', 'MySQL', 'Redis', 'Tailwind CSS', 'Stripe'],
                'role' => 'Lead Developer',
                'client_type' => 'startup',
                'industry' => 'E-Commerce',
                'year' => 2024,
                'is_featured' => true,
                'is_visible' => true,
                'status' => 'published',
                'sort_order' => 1,
                'metrics' => [
                    ['label' => 'Daily Orders', 'value' => '2,000+'],
                    ['label' => 'Response Time', 'value' => '<200ms'],
                    ['label' => 'Uptime', 'value' => '99.9%'],
                    ['label' => 'Vendors', 'value' => '150+'],
                ],
            ],
            [
                'title' => 'SaaS Analytics Dashboard',
                'slug' => 'saas-analytics-dashboard',
                'summary' => 'Real-time analytics dashboard for a B2B SaaS product, featuring customizable widgets and automated reporting.',
                'description' => '<p>Designed and built a comprehensive analytics platform that processes millions of events daily. The dashboard features drag-and-drop widget customization, scheduled report generation, and role-based access control.</p>',
                'challenge' => 'Processing and visualizing large datasets in real-time while keeping the interface responsive and intuitive.',
                'solution' => 'Leveraged event sourcing patterns, pre-aggregated metrics, and WebSocket-based live updates. Used chart.js for performant visualizations.',
                'tech_stack' => ['Laravel', 'Vue.js', 'PostgreSQL', 'Redis', 'WebSockets', 'Chart.js'],
                'role' => 'Full-Stack Developer',
                'client_type' => 'company',
                'industry' => 'SaaS',
                'year' => 2023,
                'is_featured' => true,
                'is_visible' => true,
                'status' => 'published',
                'sort_order' => 2,
                'metrics' => [
                    ['label' => 'Events/Day', 'value' => '5M+'],
                    ['label' => 'Active Users', 'value' => '8,000'],
                    ['label' => 'Dashboards', 'value' => '25K+'],
                    ['label' => 'Reports/Mo', 'value' => '50K'],
                ],
            ],
            [
                'title' => 'Healthcare Booking System',
                'slug' => 'healthcare-booking-system',
                'summary' => 'A HIPAA-aware appointment booking and patient management system for a network of medical clinics.',
                'description' => '<p>Developed a secure booking platform connecting patients with healthcare providers. Includes calendar management, automated reminders, telehealth integration, and comprehensive patient records.</p>',
                'tech_stack' => ['Laravel', 'Inertia.js', 'Vue.js', 'MySQL', 'Twilio', 'Tailwind CSS'],
                'role' => 'Lead Developer',
                'client_type' => 'company',
                'industry' => 'Healthcare',
                'year' => 2023,
                'is_featured' => true,
                'is_visible' => true,
                'status' => 'published',
                'sort_order' => 3,
            ],
        ];

        foreach ($projects as $data) {
            $project = Project::create($data);
            // Attach some skills
            $project->skills()->attach(
                collect($skills)->only(['laravel', 'vuejs', 'mysql', 'tailwind-css'])->pluck('id')
            );
        }

        // Article Categories
        $categories = [
            ArticleCategory::create(['name' => 'Engineering', 'slug' => 'engineering', 'sort_order' => 1]),
            ArticleCategory::create(['name' => 'Product', 'slug' => 'product', 'sort_order' => 2]),
            ArticleCategory::create(['name' => 'Career', 'slug' => 'career', 'sort_order' => 3]),
            ArticleCategory::create(['name' => 'Laravel', 'slug' => 'laravel', 'sort_order' => 4]),
        ];

        // Articles
        $articles = [
            [
                'title' => 'Building SEO-First Applications with Laravel and Inertia.js',
                'slug' => 'building-seo-first-applications-laravel-inertia',
                'excerpt' => 'A deep dive into building server-side rendered applications with Laravel, Inertia.js, and Vue 3 — focusing on SEO, performance, and developer experience.',
                'body' => '<h2>Why SSR Matters</h2><p>Server-side rendering is crucial for SEO and initial page load performance. In this article, we explore how to set up SSR with Laravel and Inertia.js, including meta tag management, structured data, and sitemap generation.</p><h2>The Stack</h2><p>We use Laravel as the backend, Inertia.js v3 as the bridge, and Vue 3 with TypeScript on the frontend. Tailwind CSS handles styling, and Vite powers the build system.</p><h2>Key Takeaways</h2><ul><li>Configure SSR in vite.config.ts</li><li>Handle meta tags with a composable</li><li>Generate JSON-LD structured data</li><li>Implement dynamic sitemap generation</li></ul>',
                'article_category_id' => $categories[3]->id,
                'user_id' => $user->id,
                'status' => 'published',
                'publish_at' => now()->subDays(5),
                'reading_time' => 8,
                'is_featured' => true,
            ],
            [
                'title' => 'Clean Architecture Patterns in Laravel',
                'slug' => 'clean-architecture-patterns-laravel',
                'excerpt' => 'Exploring repository patterns, service classes, DTOs, and action classes to keep your Laravel codebase maintainable as it scales.',
                'body' => '<h2>The Problem with Fat Controllers</h2><p>As Laravel applications grow, controllers tend to accumulate business logic. This article explores patterns to keep your codebase clean and testable.</p><h2>Patterns Covered</h2><ul><li>Repository Pattern</li><li>Service Classes</li><li>Data Transfer Objects</li><li>Action Classes</li><li>Form Requests as Validation Layer</li></ul><p>Each pattern is demonstrated with real-world examples from production applications.</p>',
                'article_category_id' => $categories[0]->id,
                'user_id' => $user->id,
                'status' => 'published',
                'publish_at' => now()->subDays(12),
                'reading_time' => 12,
                'is_featured' => true,
            ],
            [
                'title' => 'Lessons from Building 15+ Production Applications',
                'slug' => 'lessons-building-production-applications',
                'excerpt' => 'After years of freelancing and building production apps, here are the most impactful lessons I\'ve learned about shipping quality software.',
                'body' => '<h2>Ship Early, Iterate Often</h2><p>Perfectionism is the enemy of progress. The best applications I\'ve built were ones that shipped early and improved based on real user feedback.</p><h2>Invest in Developer Experience</h2><p>Good tooling, clear documentation, and automated testing save exponentially more time than they cost to set up.</p><h2>Performance is a Feature</h2><p>Users notice speed. A 200ms response time versus a 2-second one can make or break user retention.</p>',
                'article_category_id' => $categories[2]->id,
                'user_id' => $user->id,
                'status' => 'published',
                'publish_at' => now()->subDays(20),
                'reading_time' => 6,
                'is_featured' => true,
            ],
        ];

        foreach ($articles as $article) {
            Article::create($article);
        }

        // Social Links
        $socialLinks = [
            ['platform' => 'github', 'label' => 'GitHub', 'url' => 'https://github.com/grdzelo', 'username' => 'grdzelo', 'is_visible' => true, 'is_highlighted' => true, 'sort_order' => 1],
            ['platform' => 'linkedin', 'label' => 'LinkedIn', 'url' => 'https://linkedin.com/in/grdzelo', 'username' => 'grdzelo', 'is_visible' => true, 'is_highlighted' => true, 'sort_order' => 2],
            ['platform' => 'twitter', 'label' => 'Twitter / X', 'url' => 'https://x.com/grdzelo', 'username' => 'grdzelo', 'is_visible' => true, 'is_highlighted' => true, 'sort_order' => 3],
            ['platform' => 'telegram', 'label' => 'Telegram', 'url' => 'https://t.me/grdzelo', 'username' => 'grdzelo', 'is_visible' => true, 'is_highlighted' => false, 'sort_order' => 4],
        ];

        foreach ($socialLinks as $link) {
            SocialLink::create($link);
        }

        // Hobbies
        $hobbies = [
            [
                'title' => 'Photography',
                'slug' => 'photography',
                'summary' => 'Capturing moments and landscapes through the lens. I specialize in street photography and architectural compositions.',
                'description' => '<p>Photography has been my creative outlet for over a decade. I started with a simple DSLR and have since explored various styles and techniques.</p><p>My favorite subjects include:</p><ul><li>Street photography - capturing candid moments in urban environments</li><li>Architecture - the interplay of light, shadow, and geometric forms</li><li>Landscapes - particularly the dramatic scenery of the Caucasus mountains</li><li>Portrait photography - focusing on natural expressions</li></ul><p>I shoot primarily with Sony mirrorless cameras and edit in Lightroom. My work has been featured in several local exhibitions and online publications.</p>',
                'icon' => 'camera',
                'is_featured' => true,
                'is_visible' => true,
                'sort_order' => 1,
                'meta_title' => 'Photography - Capturing Moments & Landscapes',
                'meta_description' => 'Explore my photography journey featuring street photography, architectural shots, and landscapes from Georgia and beyond.',
            ],
            [
                'title' => 'Hiking & Nature',
                'slug' => 'hiking-nature',
                'summary' => 'Exploring mountain trails and pristine wilderness. Georgia offers some of the most breathtaking landscapes in the Caucasus region.',
                'description' => '<p>There is nothing quite like standing on a mountain peak after a long hike. The combination of physical challenge and natural beauty is unbeatable.</p><p>Favorite hiking destinations:</p><ul><li>Kazbegi - the iconic Gergeti Trinity Church trek</li><li>Svaneti - medieval towers against mountain backdrops</li><li>Tusheti - remote villages and alpine meadows</li><li>Lagodekhi - lush forests and waterfalls</li></ul><p>I typically hike from spring through autumn, ranging from day trips to multi-day expeditions. Each trail offers unique perspectives and photographic opportunities.</p>',
                'icon' => 'mountain',
                'is_featured' => true,
                'is_visible' => true,
                'sort_order' => 2,
                'meta_title' => 'Hiking & Nature - Exploring the Caucasus',
                'meta_description' => 'Join me on adventures through Georgia’s stunning mountain trails, from Kazbegi to Tusheti and beyond.',
            ],
            [
                'title' => 'Reading',
                'slug' => 'reading',
                'summary' => 'Technical books, science fiction, and philosophy. Currently juggling multiple books across different genres.',
                'description' => '<p>Reading is my primary form of learning and relaxation. I maintain a diverse reading list that spans multiple genres and subjects.</p><p>Current favorites:</p><ul><li>Technical: "Clean Architecture" by Robert C. Martin, "Designing Data-Intensive Applications" by Martin Kleppmann</li><li>Science Fiction: Works by Ted Chiang, Liu Cixin, and classic Asimov</li><li>Philosophy: Stoicism (Marcus Aurelius, Seneca), modern thinkers like Naval Ravikant</li><li>Biographies: Stories of entrepreneurs and innovators</li></ul><p>I use Goodreads to track my reading and typically aim for 20-30 books per year. I also maintain a personal knowledge base of notes and insights from my reading.</p>',
                'icon' => 'book-open',
                'is_featured' => true,
                'is_visible' => true,
                'sort_order' => 3,
                'meta_title' => 'Reading - Books That Shape My Thinking',
                'meta_description' => 'A curated list of technical books, science fiction, and philosophy that influences my work and worldview.',
            ],
            [
                'title' => 'Travel',
                'slug' => 'travel',
                'summary' => 'Exploring new cultures, cuisines, and perspectives. Travel broadens the mind and inspires creativity.',
                'description' => '<p>Travel is both a passion and a source of inspiration for my work. Experiencing different cultures helps me build more inclusive and globally-aware products.</p><p>Recent destinations include:</p><ul><li>Japan - efficiency meets tradition</li><li>Portugal - laid-back culture and beautiful coastlines</li><li>Balkans - rich history and emerging tech scenes</li><li>Central Asia - the ancient Silk Road cities</li></ul><p>I enjoy slow travel - spending weeks rather than days in a place to truly understand local culture. Each trip informs my perspective on design, architecture, and human-centered products.</p>',
                'icon' => 'plane',
                'is_featured' => false,
                'is_visible' => true,
                'sort_order' => 4,
                'meta_title' => 'Travel - Exploring the World',
                'meta_description' => 'Stories and insights from traveling across cultures and continents.',
            ],
            [
                'title' => 'Music Production',
                'slug' => 'music-production',
                'summary' => 'Creating electronic music and experimenting with sound design. A different kind of creative coding.',
                'description' => '<p>Music production is where my technical and creative sides meet. I produce electronic music in my home studio, experimenting with synthesizers, sampling, and digital audio workstations.</p><p>My setup includes:</p><ul><li>Ableton Live as my primary DAW</li><li>Hardware synths (Novation Peak, Elektron Digitone)</li><li>Field recording for unique sound textures</li><li>Modular synthesis experimentation</li></ul><p>I find that the constraints and creative problem-solving in music production often translate to better approaches in software engineering.</p>',
                'icon' => 'music',
                'is_featured' => false,
                'is_visible' => true,
                'sort_order' => 5,
                'meta_title' => 'Music Production - Sound Design & Synthesis',
                'meta_description' => 'Electronic music production, synthesizers, and the creative process behind sound design.',
            ],
        ];

        foreach ($hobbies as $hobby) {
            Hobby::create($hobby);
        }

        // Education
        $education = [
            [
                'institution' => 'Tbilisi State University',
                'degree' => 'Bachelor of Computer Science',
                'field_of_study' => 'Computer Science',
                'start_date' => '2013-09-01',
                'end_date' => '2017-06-30',
                'is_current' => false,
                'description' => 'Focus on software engineering, algorithms, and database systems. Graduated with honors.',
                'is_visible' => true,
                'sort_order' => 1,
            ],
            [
                'institution' => 'Laravel Certification Program',
                'degree' => 'Professional Certification',
                'field_of_study' => 'Laravel & PHP',
                'start_date' => '2020-01-01',
                'end_date' => '2020-06-30',
                'is_current' => false,
                'description' => 'Advanced Laravel certification covering testing, performance optimization, and architecture patterns.',
                'is_visible' => true,
                'sort_order' => 2,
            ],
        ];

        foreach ($education as $edu) {
            Education::create($edu);
        }

        // Certifications
        $certifications = [
            [
                'name' => 'AWS Certified Solutions Architect',
                'issuing_organization' => 'Amazon Web Services',
                'issue_date' => '2022-03-15',
                'expiry_date' => '2025-03-15',
                'credential_id' => 'AWS-12345-ABC',
                'credential_url' => 'https://aws.amazon.com/certification',
                'description' => 'Professional certification for designing distributed systems on AWS.',
                'is_visible' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Docker Certified Associate',
                'issuing_organization' => 'Docker',
                'issue_date' => '2021-08-20',
                'expiry_date' => '2024-08-20',
                'credential_id' => 'DCA-67890-XYZ',
                'credential_url' => 'https://docker.com/certification',
                'description' => 'Demonstrates expertise in containerization and Docker ecosystem.',
                'is_visible' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Vue.js Certification',
                'issuing_organization' => 'Vue School',
                'issue_date' => '2020-11-10',
                'credential_id' => 'VUE-CERT-2020',
                'credential_url' => 'https://vueschool.io/certification',
                'description' => 'Advanced Vue.js patterns, performance optimization, and ecosystem mastery.',
                'is_visible' => true,
                'sort_order' => 3,
            ],
        ];

        foreach ($certifications as $cert) {
            $certification = Certification::create($cert);
            $certification->skills()->attach(
                collect($skills)->only(['laravel', 'vuejs', 'docker'])->pluck('id')
            );
        }
    }
}
