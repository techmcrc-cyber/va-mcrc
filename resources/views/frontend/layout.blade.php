<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="@yield('meta_description', 'Mount Carmel Retreat Centre - Book spiritual retreats in Kerala. Experience faith renewal, inner peace, and spiritual growth in the serene Malankara Hills.')">
    <meta name="keywords" content="@yield('meta_keywords', 'retreat booking, spiritual retreat, Catholic retreat, Mount Carmel, Kerala retreat, faith renewal, spiritual growth, religious retreat, retreat centre')">
    <meta name="author" content="Mount Carmel Retreat Centre">
    <meta property="og:title" content="@yield('og_title', 'Mount Carmel Retreat Centre - Spiritual Retreats in Kerala')">
    <meta property="og:description" content="@yield('og_description', 'Book your spiritual retreat at Mount Carmel Retreat Centre in Kerala. Experience faith renewal and inner peace.')">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:image" content="@yield('og_image', asset('images/og-default.jpg'))">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('twitter_title', 'Mount Carmel Retreat Centre - Spiritual Retreats in Kerala')">
    <meta name="twitter:description" content="@yield('twitter_description', 'Book your spiritual retreat at Mount Carmel Retreat Centre in Kerala.')">
    <title>@yield('title', 'My Retreat Booking')</title>
    
    <!-- Favicons -->
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="manifest" href="{{ asset('site.webmanifest') }}">
    <meta name="theme-color" content="#ba4165">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #ba4165;
            --dark-color: #700000;
            --light-color: #d15577;
            --gradient-primary: linear-gradient(136deg, #ba4165 0%, #700000 100%);
            --light-bg: #f8f9fa;
            --cream: #ffffff;
            --beige: #f5f5f5;
            --text-dark: #2c2c2c;
            --text-light: #6b6b6b;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            color: var(--text-dark);
            background-color: var(--light-bg);
        }
        
        h1, h2, h3, h4, h5, h6 {
            font-family: 'Playfair Display', serif;
            font-weight: 600;
        }
        
        /* Navigation */
        .navbar {
            background-color: white;
            border-bottom: 1px solid #e8e8e8;
            padding: 0.5rem 0;
        }
        
        .navbar .container {
            padding-left: 0.5rem;
            padding-right: 1rem;
        }
        
        .navbar-brand {
            font-family: 'Playfair Display', serif;
            font-weight: 700;
            font-size: 1.4rem;
            color: white !important;
            margin-right: auto;
            padding: 0.5rem 1rem;
            background: rgb(186, 65, 101);
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(186, 65, 101, 0.3);
        }
        
        .navbar-nav .nav-link {
            color: var(--text-dark);
            font-weight: 500;
            margin: 0 0.5rem;
            transition: all 0.3s;
            position: relative;
            padding-bottom: 0.5rem;
        }
        
        .navbar-nav .nav-link:hover {
            color: var(--primary-color);
        }
        
        .navbar-nav .nav-link.active {
            color: var(--primary-color);
        }
        
        .navbar-nav .nav-link.active::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: var(--primary-color);
            border-radius: 2px 2px 0 0;
        }
        
        /* Buttons */
        .btn-primary {
            background: var(--gradient-primary);
            border: none;
            padding: 0.75rem 2rem;
            font-weight: 500;
            border-radius: 4px;
            transition: all 0.3s;
        }
        
        .btn-primary:hover {
            background: linear-gradient(136deg, #d15577 0%, #8a0000 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(186, 65, 101, 0.4);
        }
        
        .btn-outline-primary {
            border: 2px solid var(--primary-color);
            color: var(--primary-color);
            padding: 0.75rem 2rem;
            font-weight: 500;
            border-radius: 4px;
            transition: all 0.3s;
        }
        
        .btn-outline-primary:hover {
            background: var(--gradient-primary);
            color: white;
            border-color: transparent;
        }
        
        /* Cards */
        .card {
            border: none;
            border-radius: 8px;
            overflow: hidden;
            transition: transform 0.3s, box-shadow 0.3s;
            background-color: white;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        }
        
        /* Section Styling */
        .section-title {
            font-size: 2rem;
            margin-bottom: 0.75rem;
            color: var(--text-dark);
        }
        
        .section-subtitle {
            color: var(--text-light);
            font-size: 1rem;
            margin-bottom: 2rem;
        }
        
        /* Footer */
        footer {
            background: #1a1a1a;
            color: white;
            padding: 2.5rem 0 1.5rem;
        }
        
        footer a {
            color: #e0e0e0;
            text-decoration: none;
            transition: color 0.3s;
        }
        
        footer a:hover {
            color: white;
        }
        
        /* Icon Styling */
        .icon-box {
            width: 60px;
            height: 60px;
            background-color: white;
            border: 2px solid #f0f0f0;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
        }
        
        .icon-box i {
            font-size: 1.5rem;
            color: var(--primary-color);
        }
        
        /* Mobile Responsive Styles */
        @media (max-width: 575px) {
            .navbar-brand {
                font-size: 1.1rem;
                padding: 0.4rem 0.8rem;
            }
            
            .navbar .container {
                padding-left: 0.5rem;
                padding-right: 0.5rem;
            }
            
            .navbar-nav .nav-link {
                margin: 0.25rem 0;
            }
        }
        
        @media (max-width: 300px) {
            .navbar-brand {
                font-size: 0.95rem;
                padding: 0.3rem 0.6rem;
            }
        }
    </style>
    @stack('styles')
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}">
                MyRetreatBooking.Com
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('retreats.*') ? 'active' : '' }}" href="{{ route('retreats.index') }}">Retreats</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('booking.register') || request()->routeIs('booking.success') ? 'active' : '' }}" href="{{ route('booking.register') }}">Book Retreat</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('booking.check-status*') || request()->routeIs('booking.status') ? 'active' : '' }}" href="{{ route('booking.check-status') }}">Check Status</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    @yield('content')

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <h5 class="mb-3" style="font-family: 'Playfair Display', serif; color: white;">My Retreat Booking</h5>
                    <p style="color: #e0e0e0; line-height: 1.8;">
                        Offering sacred spaces for spiritual growth and inner peace. 
                        Join us on a journey of self-discovery and renewal.
                    </p>
                </div>
                <div class="col-md-4 mb-4">
                    <h6 class="mb-3" style="color: white;">Quick Links</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="{{ route('home') }}">Home</a></li>
                        <li class="mb-2"><a href="{{ route('retreats.index') }}">Retreats</a></li>
                        <li class="mb-2"><a href="{{ route('booking.register') }}">Register</a></li>
                        <li class="mb-2"><a href="{{ route('booking.check-status') }}">Check Status</a></li>
                    </ul>
                </div>
                <div class="col-md-4 mb-4">
                    <h6 class="mb-3" style="color: white;">Contact</h6>
                    <p style="color: #e0e0e0;">
                        <i class="fas fa-envelope me-2"></i> booking@mountcarmelretreatcentre.org<br>
                        <i class="fas fa-phone me-2"></i> Retreat Bookings: +91 9446 113 725<br>
                        <i class="fas fa-phone me-2"></i> Programme Enquiries: +91 8281 101 101<br>
                    </p>
                </div>
            </div>
            <hr style="border-color: rgba(255,255,255,0.2); margin: 2rem 0 1rem;">
            <div class="text-center" style="color: #e0e0e0; font-size: 0.9rem;">
                <p class="mb-0">&copy; {{ date('Y') }} My Retreat Booking. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
