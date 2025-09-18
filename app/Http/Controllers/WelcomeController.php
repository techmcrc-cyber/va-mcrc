<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WelcomeController extends Controller
{
    /**
     * Display the welcome/landing page
     */
    public function index()
    {
        $quotes = [
            [
                'text' => "For where two or three gather in my name, there am I with them.",
                'verse' => "Matthew 18:20"
            ],
            [
                'text' => "Let us not give up meeting together, as some are in the habit of doing, but let us encourage one another.",
                'verse' => "Hebrews 10:25"
            ],
            [
                'text' => "I was glad when they said to me, 'Let us go to the house of the Lord!'",
                'verse' => "Psalm 122:1"
            ],
            [
                'text' => "But seek first the kingdom of God and his righteousness, and all these things will be added to you.",
                'verse' => "Matthew 6:33"
            ],
            [
                'text' => "Trust in the Lord with all your heart, and do not lean on your own understanding.",
                'verse' => "Proverbs 3:5"
            ],
            [
                'text' => "The Lord is my shepherd; I shall not want.",
                'verse' => "Psalm 23:1"
            ]
        ];

        $quote = $quotes[array_rand($quotes)];

        return view('welcome-original', [
            'quote' => $quote,
            'features' => [
                [
                    'icon' => 'sparkles',
                    'title' => 'Spiritual Growth',
                    'description' => 'Deepen your faith through guided retreats and spiritual exercises.'
                ],
                [
                    'icon' => 'users',
                    'title' => 'Community',
                    'description' => 'Connect with like-minded individuals on the same spiritual journey.'
                ],
                [
                    'icon' => 'calendar',
                    'title' => 'Easy Scheduling',
                    'description' => 'Manage your retreat schedule and activities with ease.'
                ]
            ]
        ]);
    }
}
