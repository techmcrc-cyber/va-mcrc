<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Merriweather:wght@400;700&display=swap" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        :root {
            --primary: #7f1d1d;
            --primary-light: #fef2f2;
            --accent: #b91c1c;
            --dark: #1f2937;
            --light: #f9fafb;
        }
        
        body {
            font-family: 'Nunito', sans-serif;
            color: #4a5568;
            line-height: 1.6;
            background-color: #f8f9fc;
            margin: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        .hero {
            background: linear-gradient(135deg, #ba4165 0%, #700000  100%);
            position: relative;
            overflow: hidden;
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 3rem 1rem 4rem;
        }
        
        .hero::after {
            content: '';
            position: absolute;
            top: 20px;
            right: 20px;
            width: 100px;
            height: 100px;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='rgba(255,255,255,0.1)'%3E%3Cpath d='M12 2L2 22h20L12 2zm0 3.5L18.5 20h-13L12 5.5z'/%3E%3C/svg%3E");
            background-size: contain;
            background-repeat: no-repeat;
            background-position: center;
            opacity: 0.8;
        }
        
        .hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url("data:image/svg+xml,%3Csvg width='100' height='100' viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM34 90c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm56-76c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM12 86c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm28-65c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm23-11c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-6 60c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm29-22c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zM32 63c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm57-13c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-9-21c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM60 91c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM35 41c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM12 60c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2z' fill='%23b91c1c' fill-opacity='0.1' fill-rule='evenodd'/%3E%3C/svg%3E");
            opacity: 0.8;
        }
        
        .logo-container {
            max-width: 160px;
            margin: 0 auto 1.5rem;
            padding: 1.25rem;
            background: rgba(255, 255, 255, 0.9);
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .logo-container img {
            max-height: 100%;
            width: auto;
            display: block;
            margin: 0 auto;
        }
        
        h1 {
            font-size: 2rem;
            font-weight: 700;
            color: white;
            margin-bottom: 1.2rem;
            line-height: 1.2;
        }
        
        .tagline {
            font-size: 1rem;
            color: rgba(255, 255, 255, 0.9);
            margin-bottom: 1.5rem;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }
        
        .quote-box {
            background: rgba(255, 255, 255, 0.95);
            border-left: 4px solid #4f46e5;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            max-width: 700px;
            margin: 0 auto 2.5rem;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .quote-text {
            font-family: 'Merriweather', serif;
            font-size: 1.25rem;
            font-weight: 400;
            line-height: 1.6;
            color: var(--accent);
            margin-bottom: 0.8rem;
        }
        
        .quote-verse {
            color: var(--accent);
            font-weight: 600;
            font-size: 0.95rem;
        }
        
        .btn {
            display: inline-block;
            font-weight: 600;
            text-align: center;
            white-space: nowrap;
            vertical-align: middle;
            user-select: none;
            border: 2px solid transparent;
            padding: 0.6rem 1.75rem;
            font-size: 0.9rem;
            line-height: 1.5;
            border-radius: 8px;
            transition: all 0.3s ease;
            text-decoration: none;
            cursor: pointer;
        }
        
        .btn-primary {
            background: white;
            color: #b41b1b;
            font-weight: 600;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            font-size: 0.85rem;
            padding: 0.75rem 2rem;
            border-radius: 6px;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.15);
            background: #f8f9fa;
        }
        
        .btn-outline {
            background: transparent;
            color: white;
            border: 2px solid white;
        }
        
        .btn-outline:hover {
            background: white;
            color: var(--primary);
        }
        
        .btn-group {
            display: flex;
            flex-direction: column;
            gap: 1rem;
            justify-content: center;
            margin-top: 2rem;
        }
        
        @media (min-width: 640px) {
            h1 {
                font-size: 2.75rem;
            }
            
            .tagline {
                font-size: 1.25rem;
            }
            
            .btn-group {
                flex-direction: row;
            }
        }
    </style>
</head>
<body>
    <!-- Hero Section -->
    <div class="hero text-white py-16 md:py-24 relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center mb-4">
                <div class="logo-container inline-block mb-8">
                    <img src="https://mountcarmelretreatcentre.org/wp-content/uploads/2022/02/logo_mcrc_new-1-100x118.png" 
                         alt="Mount Carmel Retreat Centre" 
                         >
                </div>
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold mb-2">
                    Welcome to {{ config('app.name') }}
                </h1>
                <p class="text-xl md:text-2xl opacity-90 max-w-3xl mx-auto mb-4">
                    A sacred space for spiritual growth and community
                </p>
            </div>
            <div class="quote-box">
                <p class="quote-text">{{ $welcome['text'] }}</p>
            </div>

            <div class="btn-group">
                <a href="/login" class="btn btn-primary">Admin Login</a>
            </div>
        </div>
    </div>
    <!-- Scripts -->
    <script>
        // Mobile menu functionality
        const mobileMenuButton = document.querySelector('[aria-controls="mobile-menu"]');
        const mobileMenu = document.getElementById('mobile-menu');
        
        mobileMenuButton.addEventListener('click', function() {
            const expanded = this.getAttribute('aria-expanded') === 'true' || false;
            this.setAttribute('aria-expanded', !expanded);
            mobileMenu.classList.toggle('hidden');
        });
    </script>
</body>
</html>
