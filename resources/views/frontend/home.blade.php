@extends('frontend.layout')

@section('title', 'Mount Carmel Retreat Centre - Spiritual Retreats in Kerala')
@section('meta_description', 'Book your spiritual retreat at Mount Carmel Retreat Centre in the serene Malankara Hills, Kerala. Experience faith renewal, inner peace, and spiritual growth through Scripture and Sacrament.')
@section('meta_keywords', 'Mount Carmel Retreat Centre, spiritual retreat Kerala, Catholic retreat, faith renewal, Malankara Hills, religious retreat, Bible retreat, spiritual growth, retreat booking Kerala, Syro-Malankara Catholic Church')
@section('og_title', 'Mount Carmel Retreat Centre - Draw Near to God')
@section('og_description', 'Set amidst the serene Malankara Hills in Kerala, Mount Carmel Retreat Centre welcomes pilgrims and seekers from around the world to encounter the life-giving power of Scripture and Sacrament.')

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
    
    /* Mobile Responsive Styles */
    @media (max-width: 575px) {
        .hero-section {
            padding: 1.5rem 0;
        }
        
        .hero-content {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .hero-content h1 {
            font-size: 2rem;
            line-height: 1.3;
        }
        
        .hero-content p {
            font-size: 0.95rem;
        }
        
        .booking-form-card {
            margin: 0 auto;
            max-width: 400px;
        }
        
        .guide-section {
            padding: 1.5rem 0;
        }
        
        .guide-image {
            width: 150px;
            height: 150px;
            margin: 0 auto 1rem;
        }
        
        .section-title {
            font-size: 1.75rem;
        }
        
        .section-subtitle {
            font-size: 0.95rem;
        }
        
        .stat-item h2 {
            font-size: 2rem !important;
        }
        
        .stat-item p {
            font-size: 0.85rem !important;
        }
        
        .retreat-card-body {
            padding: 1.5rem;
        }
        
        .container-fluid {
            padding-left: 1rem !important;
            padding-right: 1rem !important;
        }
    }
    
    @media (max-width: 300px) {
        .hero-content h1 {
            font-size: 1.5rem;
        }
        
        .booking-form-card {
            padding: 1rem;
        }
        
        .booking-form-card h3 {
            font-size: 1.25rem;
        }
        
        .btn-lg {
            padding: 0.75rem 1rem;
            font-size: 0.9rem;
        }
        
        .stat-item h2 {
            font-size: 1.5rem !important;
        }
        
        .guide-image {
            width: 120px;
            height: 120px;
        }
    }
</style>
@endpush

@section('content')
<!-- Hero Section with Booking Form -->
<div class="hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 hero-content">
                <h1>Draw Near to God</h1>
                <p>
                    Set amidst the serene Malankara Hills in Kerala, Mount Carmel Retreat Centre is a sacred space where hearts are restored and faith is renewed. Rooted in the Catholic tradition and guided by the Syro-Malankara Catholic Church, it welcomes pilgrims, seekers and believers from around the world to encounter the life-giving power of Scripture and Sacrament.
                </p>
            </div>
            <div class="col-lg-6">
                <div class="booking-form-card">
                    <h3>Secure Your Spots.</h3>
                    <!-- <p class="subtitle">Choose an action to get started</p> -->
                    
                    <div class="d-grid gap-3">
                        <a href="{{ route('booking.register') }}" class="btn btn-primary btn-lg">
                            <i class="fas fa-calendar-plus me-2"></i>
                            Book My Retreat
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
                            <a href="mailto:booking@mountcarmelretreatcentre.org" style="color: var(--primary-color); text-decoration: none;">
                                Contact us at booking@mountcarmelretreatcentre.org
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
                    <h2 class="mb-2 counter" data-target="16000" style="font-size: 3rem; font-weight: 700; color: var(--text-dark);">0</h2>
                    <p class="mb-0" style="font-size: 1.1rem; color: var(--text-light);">Residential Retreat Participants</p>
                </div>
            </div>
            <div class="col-md-3 col-6 mb-4 mb-md-0">
                <div class="stat-item">
                    <i class="fas fa-calendar-check fa-3x mb-3" style="color: var(--primary-color);"></i>
                    <h2 class="mb-2 counter" data-target="5" style="font-size: 3rem; font-weight: 700; color: var(--text-dark);">0</h2>
                    <p class="mb-0" style="font-size: 1.1rem; color: var(--text-light); white-space: nowrap;">Bible Crash Courses Conducted</p>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stat-item">
                    <i class="fas fa-star fa-3x mb-3" style="color: var(--primary-color);"></i>
                    <h2 class="mb-2 counter" data-target="2000" style="font-size: 3rem; font-weight: 700; color: var(--text-dark);">0</h2>
                    <p class="mb-0" style="font-size: 1.1rem; color: var(--text-light);">Visitors on Convention Days</p>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stat-item">
                    <i class="fas fa-heart fa-3x mb-3" style="color: var(--primary-color);"></i>
                    <h2 class="mb-2 counter" data-target="500000" style="font-size: 3rem; font-weight: 700; color: var(--text-dark);">0</h2>
                    <p class="mb-0" style="font-size: 1.1rem; color: var(--text-light);">followers</p>
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
            <h2 class="section-title">Meet the Leadership</h2>
            <!-- <p class="section-subtitle">Every mission with a divine purpose needs inspired leadership. At the heart of Mount Carmel Retreat Centre is a team deeply rooted in Scripture. </p> -->
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
                        The Spiritual Director of Mount Carmel Retreat Centre, he is known for his deep love of Scripture. His approachable nature and inspiring faith have made him especially dear to the youth. His presence across television and digital platforms continues to draw people closer to the Word of God and to the heart of the Church. He also travels widely, leading retreats and faith encounters that strengthen communities and renew souls across the world.
                    </p>
                </div>
            </div>

            <!-- Guide 2 -->
            <div class="col-md-4">
                <div class="text-center">
                    <div class="guide-image mx-auto mb-3" style="width: 200px; height: 200px;">
                        <img src="{{ asset('images/fr_adarsh.jpg') }}" alt="Fr. Adarsh Kumbalathu">
                    </div>
                    <h4 class="mb-2">Fr. Adarsh Kumbalathu</h4>
                    <p style="color: var(--primary-color);" class="mb-3">Preacher</p>
                    <p style="color: var(--text-light);">
                        As Minister at Mount Carmel Retreat Centre, Fr Adarsh brings youthful energy and a compassionate spirit to the ministry. His presence helps bridge faith and everyday life, guiding retreatants to experience God’s love through simplicity, service and joy.
                    </p>
                </div>
            </div>

            <!-- Guide 3 -->
            <div class="col-md-4">
                <div class="text-center">
                    <div class="guide-image mx-auto mb-3" style="width: 200px; height: 200px;">
                        <img src="{{ asset('images/fr_wilson.jpg') }}" alt="Fr. Wilson Thattaruthundil">
                    </div>
                    <h4 class="mb-2">Fr. Wilson Thattaruthundil</h4>
                    <p style="color: var(--primary-color);" class="mb-3">Preacher</p>
                    <p style="color: var(--text-light);">
                        Serving as Administrator of the Retreat Centre, Fr. Wilson handles the organisational, logistical and administrative aspects that allow MCRC’s spiritual programmes to run smoothly and effectively. His leadership ensures the peaceful, retreat-friendly atmosphere of the campus in Vettinad
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
        <h2 class="section-title">Upcoming Retreats</h2>
<!--         <p class="section-subtitle">Choose the perfect spiritual experience for your journey</p>-->
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
                        <li><i class="fas fa-filter"></i>Eligibility : {{ $retreat['criteria_name'] ?? 'Open to all' }}</li>
                        <li><i class="fas fa-users"></i>Availability : {{ $retreat['available_spots'] }} spots remaining</li>
                        <!-- <li><i class="fas fa-clock"></i>Time : {{ $retreat['timings'] ?? 'Check details' }}</li> -->
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
            <h2 class="section-title">Discover other Catholic retreat centres.</h2>
        </div>
        
        <div class="row g-3">
            <div class="col-md-4">
                <div class="card p-4">
                    <h5 class="mb-3">Marian Retreat Centre, Anakkara</h5>
                    <p style="color: var(--text-light);" class="mb-4">
                        Situated in the peaceful village of Anakkara in Idukki district, Marian Retreat Centre is a haven of grace and healing.Through prayer, preaching and the power of the Holy Spirit, thousands have experienced renewal and deep conversion here.The centre is known for its Marian devotion and its ministry of intercession and inner healing.
                    </p>
                    <a href="https://marianretreatcentre.org/residential-retreats/" class="btn btn-outline-primary" target="_blank">
                        Visit Website →
                    </a>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card p-4">
                    <h5 class="mb-3">Sehion Retreat Centre, Palakkad</h5>
                    <p style="color: var(--text-light);" class="mb-4">
                       Founded under the guidance of Fr. Xavier Khan Vattayil, Sehion Retreat Centre is one of the most vibrant evangelisation ministries in India.Rooted in charismatic spirituality, Sehion reaches millions through retreats, missions and media, spreading the fire of God’s love across the world.It continues to inspire the faithful to live in the fullness of the Holy Spirit.
                    </p>
                    <a href="https://www.sehion.in/" class="btn btn-outline-primary" target="_blank">
                        Visit Website →
                    </a>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card p-4">
                    <h5 class="mb-3">Divine Retreat Centre</h5>
                    <p style="color: var(--text-light);" class="mb-4">
                       A Catholic retreat centre on the serene banks of the Chalakudy River in Kerala, run by the Vincentian Congregation’s Mary Matha Province. For over three decades, it has welcomed thousands seeking spiritual renewal and healing in Christ. Rooted in the mission of Jesus, it continues to share His peace and love through the power of the Holy Spirit.
                    </p>
                    <a href="https://drcm.org/retreats/#upcomingretreats" class="btn btn-outline-primary" target="_blank">
                        Visit Website →
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
