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
        $welcomeMessage = [
            'text' => "Experience spiritual renewal and growth at our peaceful retreat center, where faith and community come together.",
            'tagline' => "A Sacred Space for Reflection and Growth"
        ];

        return view('welcome-original', [
            'welcome' => $welcomeMessage,
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
