<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Page;

class PageController extends Controller
{
    public function show($slug)
    {
        $titles = [
            'about' => 'About Us',
            'pricing' => 'Pricing',
            'help-center' => 'Help Center',
            'privacy-policy' => 'Privacy Policy',
            'terms-of-service' => 'Terms of Service',
            'contact-us' => 'Contact Us',
        ];

        if (!array_key_exists($slug, $titles)) {
            abort(404);
        }

        $page = Page::firstOrCreate(
            ['slug' => $slug],
            [
                'title' => $titles[$slug],
                'content' => '
                    <div class="prose prose-invert max-w-none prose-orange">
                        <p class="text-stone-400 leading-relaxed mb-6">Welcome to the ' . $titles[$slug] . ' page. This content is dynamically loaded from the database.</p>
                        <h3 class="text-2xl font-bold text-white mt-8 mb-4">1. Introduction</h3>
                        <p class="text-stone-400 leading-relaxed mb-6">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>
                        <h3 class="text-2xl font-bold text-white mt-8 mb-4">2. General Information</h3>
                        <p class="text-stone-400 leading-relaxed mb-6">Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
                        <ul class="list-disc list-inside text-stone-400 mb-6 space-y-2">
                            <li>Dynamic content management</li>
                            <li>Secure data storage</li>
                            <li>Easy updates through admin panel</li>
                        </ul>
                        <p class="text-stone-400 leading-relaxed mb-6">You can easily update this content directly in your database\'s <code class="text-orange-400 bg-white/5 px-1 py-0.5 rounded">pages</code> table.</p>
                    </div>
                '
            ]
        );

        return view('pages.show', compact('page'));
    }
}
