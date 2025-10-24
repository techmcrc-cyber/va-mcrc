<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'My Retreat Booking')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-green: #2d5f4f;
            --dark-green: #1e3f33;
            --light-green: #3d7f6f;
            --cream: #f5f3ed;
            --beige: #e8e4d9;
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
            background-color: var(--cream);
        }
        
        h1, h2, h3, h4, h5, h6 {
            font-family: 'Playfair Display', serif;
            font-weight: 600;
        }
        
        /* Navigation */
        .navbar {
            background-color: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            padding: 1rem 0;
        }
        
        .navbar-brand {
            font-family: 'Playfair Display', serif;
            font-weight: 700;
            font-size: 1.3rem;
            color: var(--primary-green) !important;
        }
        
        .navbar-nav .nav-link {
            color: var(--text-dark);
            font-weight: 500;
            margin: 0 0.5rem;
            transition: color 0.3s;
        }
        
        .navbar-nav .nav-link:hover {
            color: var(--primary-green);
        }
        
        /* Buttons */
        .btn-primary {
            background-color: var(--primary-green);
            border: none;
            padding: 0.75rem 2rem;
            font-weight: 500;
            border-radius: 4px;
            transition: all 0.3s;
        }
        
        .btn-primary:hover {
            background-color: var(--dark-green);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(45, 95, 79, 0.3);
        }
        
        .btn-outline-primary {
            border: 2px solid var(--primary-green);
            color: var(--primary-green);
            padding: 0.75rem 2rem;
            font-weight: 500;
            border-radius: 4px;
            transition: all 0.3s;
        }
        
        .btn-outline-primary:hover {
            background-color: var(--primary-green);
            color: white;
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
            background-color: var(--dark-green);
            color: white;
            padding: 2.5rem 0 1.5rem;
            margin-top: 3rem;
        }
        
        footer a {
            color: var(--beige);
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
            background-color: var(--beige);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
        }
        
        .icon-box i {
            font-size: 1.5rem;
            color: var(--primary-green);
        }
    </style>
    @stack('styles')
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}">
                My Retreat Booking
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('home') }}">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('retreats.index') }}">Retreats</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('booking.check-status') }}">Check Status</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('booking.register') }}">Contact</a>
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
                    <h5 class="mb-3" style="font-family: 'Playfair Display', serif;">Sacred Retreats</h5>
                    <p style="color: var(--beige); line-height: 1.8;">
                        Offering sacred spaces for spiritual growth and inner peace. 
                        Join us on a journey of self-discovery and renewal.
                    </p>
                </div>
                <div class="col-md-4 mb-4">
                    <h6 class="mb-3">Quick Links</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="{{ route('home') }}">Home</a></li>
                        <li class="mb-2"><a href="{{ route('retreats.index') }}">Retreats</a></li>
                        <li class="mb-2"><a href="{{ route('booking.register') }}">Register</a></li>
                        <li class="mb-2"><a href="{{ route('booking.check-status') }}">Check Status</a></li>
                    </ul>
                </div>
                <div class="col-md-4 mb-4">
                    <h6 class="mb-3">Contact</h6>
                    <p style="color: var(--beige);">
                        <i class="fas fa-envelope me-2"></i> info@myretreatbooking.com<br>
                        <i class="fas fa-phone me-2"></i> +1 (555) 123-4567<br>
                        <i class="fas fa-map-marker-alt me-2"></i> Find your journey here
                    </p>
                </div>
            </div>
            <hr style="border-color: rgba(255,255,255,0.1); margin: 2rem 0 1rem;">
            <div class="text-center" style="color: var(--beige); font-size: 0.9rem;">
                <p class="mb-0">&copy; {{ date('Y') }} Sacred Retreats - My Retreat Booking. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
