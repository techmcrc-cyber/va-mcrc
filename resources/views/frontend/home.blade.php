@extends('frontend.layout')

@section('title', 'My Retreat Booking')

@push('styles')
<style>
    .hero-section {
        background: #f8f9fa;
        color: var(--text-dark);
        padding: 2rem 0 0 0;
        position: relative;
    }
    
    .hero-content h1 {
        font-size: 3.5rem;
        line-height: 1.2;
        margin-bottom: 1rem;
        font-weight: 600;
        color: var(--text-dark);
    }
    
    .hero-content p {
        font-size: 1rem;
        line-height: 1.6;
        color: var(--text-light);
        margin-bottom: 1.5rem;
    }
    
    .booking-form-card {
        background: #fafafa;
        border-radius: 12px;
        padding: 1.5rem;
        box-shadow: 0 10px 40px rgba(0,0,0,0.15);
    }
    
    .booking-form-card h3 {
        color: var(--text-dark);
        margin-bottom: 0.5rem;
        font-size: 1.5rem;
    }
    
    .booking-form-card .subtitle {
        color: var(--text-light);
        font-size: 0.95rem;
        margin-bottom: 1.5rem;
    }
    
    .booking-form-card .btn-lg {
        padding: 0.875rem 1.5rem;
        font-size: 1rem;
        font-weight: 500;
        border-radius: 8px;
        transition: all 0.3s;
    }
    
    .booking-form-card .btn-primary {
        background: var(--gradient-primary);
        border: none;
    }
    
    .booking-form-card .btn-primary:hover {
        background: linear-gradient(136deg, #d15577 0%, #8a0000 100%);
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(186, 65, 101, 0.4);
    }
    
    .booking-form-card .btn-outline-primary {
        border: 2px solid var(--primary-color);
        color: var(--primary-color);
        background-color: white;
    }
    
    .booking-form-card .btn-outline-primary:hover {
        background: var(--gradient-primary);
        color: white;
        border-color: transparent;
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(186, 65, 101, 0.3);
    }
    
    .guide-section {
        padding: 2rem 0;
        background-color: white;
    }
    
    .guide-content {
        display: flex;
        align-items: center;
        gap: 3rem;
    }
    
    .guide-image {
        width: 200px;
        height: 200px;
        border-radius: 50%;
        background-color: #f0f0f0;
        border: 3px solid var(--beige);
        overflow: hidden;
        flex-shrink: 0;
    }
    
    .guide-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .retreat-card {
        background: white;
        border-radius: 8px;
        overflow: hidden;
        height: 100%;
    }
    
    .retreat-icon {
        background-color: white;
        padding: 3rem 0;
        text-align: center;
        border-bottom: 1px solid #f0f0f0;
    }
    
    .retreat-icon i {
        font-size: 2.5rem;
        color: var(--primary-color);
    }
    
    .retreat-card-body {
        padding: 2rem;
    }
    
    .retreat-card h5 {
        font-size: 1.3rem;
        margin-bottom: 1rem;
    }
    
    .retreat-features {
        list-style: none;
        padding: 0;
        margin: 1rem 0;
    }
    
    .retreat-features li {
        padding: 0.5rem 0;
        color: var(--text-light);
        font-size: 0.95rem;
    }
    
    .retreat-features li i {
        color: var(--primary-color);
        margin-right: 0.5rem;
    }
</style>
@endpush

@section('content')
<!-- Hero Section with Booking Form -->
<div class="hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 hero-content">
                <h1>Book your journey<br>to spiritual clarity</h1>
                <p>
                    Discover peace through guided spiritual experiences in serene 
                    natural settings. Begin your journey toward inner transformation 
                    and lasting peace.
                </p>
            </div>
            <div class="col-lg-6">
                <div class="booking-form-card">
                    <h3>Secure Your Spot</h3>
                    <p class="subtitle">Choose an action to get started</p>
                    
                    <div class="d-grid gap-3">
                        <a href="{{ route('booking.register') }}" class="btn btn-primary btn-lg">
                            <i class="fas fa-calendar-plus me-2"></i>
                            Book a Retreat
                        </a>
                        
                        <a href="{{ route('booking.check-status') }}" class="btn btn-primary btn-lg">
                            <i class="fas fa-search me-2"></i>
                            Check Status & Cancellation
                        </a>
                    </div>
                    
                    <div class="mt-4 pt-3" style="border-top: 1px solid #e0e0e0;">
                        <p class="text-center mb-2" style="color: var(--text-light); font-size: 0.9rem;">
                            <i class="fas fa-info-circle me-1"></i> Need help?
                        </p>
                        <p class="text-center mb-0" style="font-size: 0.9rem;">
                            <a href="mailto:info@myretreatbooking.com" style="color: var(--primary-color); text-decoration: none;">
                                Contact us at info@myretreatbooking.com
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Statistics Section -->
<div style="background-color: #f8f9fa; padding: 3rem 0;">
    <div class="container">
        <div class="row text-center">
            <div class="col-md-3 col-6 mb-4 mb-md-0">
                <div class="stat-item">
                    <i class="fas fa-users fa-3x mb-3" style="color: var(--primary-color);"></i>
                    <h2 class="mb-2 counter" data-target="400" style="font-size: 3rem; font-weight: 700; color: var(--text-dark);">0</h2>
                    <p class="mb-0" style="font-size: 1.1rem; color: var(--text-light);">Participants</p>
                </div>
            </div>
            <div class="col-md-3 col-6 mb-4 mb-md-0">
                <div class="stat-item">
                    <i class="fas fa-calendar-check fa-3x mb-3" style="color: var(--primary-color);"></i>
                    <h2 class="mb-2 counter" data-target="50" style="font-size: 3rem; font-weight: 700; color: var(--text-dark);">0</h2>
                    <p class="mb-0" style="font-size: 1.1rem; color: var(--text-light);">Retreats Completed</p>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stat-item">
                    <i class="fas fa-star fa-3x mb-3" style="color: var(--primary-color);"></i>
                    <h2 class="mb-2 counter" data-target="15" style="font-size: 3rem; font-weight: 700; color: var(--text-dark);">0</h2>
                    <p class="mb-0" style="font-size: 1.1rem; color: var(--text-light);">Years Experience</p>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stat-item">
                    <i class="fas fa-heart fa-3x mb-3" style="color: var(--primary-color);"></i>
                    <h2 class="mb-2 counter" data-target="98" style="font-size: 3rem; font-weight: 700; color: var(--text-dark);">0</h2>
                    <p class="mb-0" style="font-size: 1.1rem; color: var(--text-light);">Satisfaction Rate</p>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const counters = document.querySelectorAll('.counter');
    const speed = 200; // Animation speed
    
    const animateCounter = (counter) => {
        const target = +counter.getAttribute('data-target');
        const increment = target / speed;
        let count = 0;
        
        const updateCount = () => {
            count += increment;
            if (count < target) {
                counter.innerText = Math.ceil(count);
                setTimeout(updateCount, 10);
            } else {
                counter.innerText = target + (counter.parentElement.querySelector('p').innerText.includes('%') ? '%' : '+');
            }
        };
        
        updateCount();
    };
    
    // Intersection Observer to trigger animation when section is visible
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const counter = entry.target;
                if (counter.innerText === '0') {
                    animateCounter(counter);
                }
            }
        });
    }, { threshold: 0.5 });
    
    counters.forEach(counter => observer.observe(counter));
});
</script>
@endpush

<!-- Guide Section -->
<div class="guide-section">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="section-title">Guiding Your Retreat</h2>
            <p class="section-subtitle">Meet the guides who will support you on your journey</p>
        </div>
        
        <div class="row g-4">
            <!-- Guide 1 -->
            <div class="col-md-4">
                <div class="text-center">
                    <div class="guide-image mx-auto mb-3" style="width: 200px; height: 200px;">
                        <img src="{{ asset('images/fr_daniel.jpg') }}" alt="Fr. Daniel Poovannathil">
                    </div>
                    <h4 class="mb-2">Fr. Daniel Poovannathil</h4>
                    <p style="color: var(--primary-color);" class="mb-3">Spiritual Director</p>
                    <p style="color: var(--text-light);">
                        With over 15 years of experience in practices and spiritual direction, 
                        bringing wisdom and compassion to each retreat.
                    </p>
                </div>
            </div>

            <!-- Guide 2 -->
            <div class="col-md-4">
                <div class="text-center">
                    <div class="guide-image mx-auto mb-3" style="width: 200px; height: 200px;">
                        <img src="{{ asset('images/fr_adarsh.jpg') }}" alt="fr.adarsh">
                    </div>
                    <h4 class="mb-2">Fr. Adarsh</h4>
                    <p style="color: var(--primary-color);" class="mb-3">Meditation Instructor</p>
                    <p style="color: var(--text-light);">
                        Trained in Ignatian spirituality and mindfulness practices, creating a safe and 
                        nurturing environment for personal growth.
                    </p>
                </div>
            </div>

            <!-- Guide 3 -->
            <div class="col-md-4">
                <div class="text-center">
                    <div class="guide-image mx-auto mb-3" style="width: 200px; height: 200px;">
                        <img src="{{ asset('images/fr_wilson.jpg') }}" alt="fr.wilson">
                    </div>
                    <h4 class="mb-2">Fr. Wilson</h4>
                    <p style="color: var(--primary-color);" class="mb-3">Retreat Coordinator</p>
                    <p style="color: var(--text-light);">
                        Blending ancient wisdom with modern understanding, making the retreat 
                        experience accessible and profound for all seekers.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Featured Retreats -->
@if($upcomingRetreats->count() > 0)
<div class="container" style="padding: 3rem 0;">
    <div class="text-center mb-4">
        <h2 class="section-title">Featured Retreats</h2>
        <p class="section-subtitle">Choose the perfect spiritual experience for your journey</p>
    </div>
    
    <div class="row g-4">
        @foreach($upcomingRetreats->take(3) as $retreat)
        <div class="col-md-4">
            <div class="retreat-card">
                <div class="retreat-card-body">
                    <h5>{{ $retreat['retreat_name'] }}</h5>
                    <p style="color: var(--text-light); font-size: 0.95rem; margin-bottom: 1rem;">
                        {{ \Carbon\Carbon::parse($retreat['start_date'])->format('M d') }} - 
                        {{ \Carbon\Carbon::parse($retreat['end_date'])->format('M d, Y') }}
                    </p>
                    <ul class="retreat-features">
                        <li><i class="fas fa-filter"></i> Criteria: {{ $retreat['criteria_name'] ?? 'Open to all' }}</li>
                        <li><i class="fas fa-users"></i> {{ $retreat['available_spots'] }} spots available</li>
                        <li><i class="fas fa-clock"></i> {{ $retreat['timings'] ?? 'Check details' }}</li>
                    </ul>
                    @if($retreat['available_spots'] > 0)
                        <a href="{{ route('retreats.show', $retreat['retreat_id']) }}" class="btn btn-outline-primary w-100" style="padding: 0.375rem 0.75rem;">
                            Book Now
                        </a>
                    @else
                        <button class="btn btn-secondary w-100" style="padding: 0.375rem 0.75rem;" disabled>
                            <i class="fas fa-times-circle"></i> Slots Filled
                        </button>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
    
    <div class="text-center mt-4">
        <a href="{{ route('retreats.index') }}" class="btn btn-primary btn-lg">
            View All Retreats
        </a>
    </div>
</div>
@endif

<!-- Explore Other Spaces -->
<div style="padding: 3rem 0; background-color: white;">
    <div class="container-fluid" style="max-width: 1400px; padding-left: 2rem; padding-right: 2rem;">
        <div class="text-center mb-4">
            <h2 class="section-title">Explore Other Sacred Spaces</h2>
            <p class="section-subtitle">Discover additional retreat centers in our network</p>
        </div>
        
        <div class="row g-3">
            <div class="col-md-4">
                <div class="card p-4">
                    <h5 class="mb-3">Zen Mountain Monastery</h5>
                    <p style="color: var(--text-light);" class="mb-4">
                        Experience traditional Zen practice in a serene mountain setting. 
                        Perfect for those seeking deep meditation and mindfulness training.
                    </p>
                    <a href="{{ route('retreats.index') }}" class="btn btn-outline-primary">
                        Visit Website →
                    </a>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card p-4">
                    <h5 class="mb-3">Spirit Rock Meditation Center</h5>
                    <p style="color: var(--text-light);" class="mb-4">
                        A beautiful sanctuary for insight meditation and Buddhist teachings. 
                        Located on 400 acres of rolling hills in Marin County.
                    </p>
                    <a href="{{ route('retreats.index') }}" class="btn btn-outline-primary">
                        Visit Website →
                    </a>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card p-4">
                    <h5 class="mb-3">Sacred Heart Retreat Center</h5>
                    <p style="color: var(--text-light);" class="mb-4">
                        A peaceful haven for contemplative prayer and spiritual renewal. 
                        Offering guided retreats in a tranquil natural setting.
                    </p>
                    <a href="{{ route('retreats.index') }}" class="btn btn-outline-primary">
                        Visit Website →
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
