<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>EasyStock</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Font Awesome (for other icons like social media, dashboard, etc.) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- AOS Animation -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <style>
        :root {
            --primary: #2A5C7D;
            --primary-dark: #1E4560;
            --secondary: #4ECDC4;
            --accent: #FF6B6B;
            --dark: #2C3E50;
            --light: #F7F9FC;
            --success: #27AE60;
            --warning: #F39C12;
            --danger: #E74C3C;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            color: var(--dark);
            padding-top: 80px;
            overflow-x: hidden;
        }
        
        .navbar {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 20px rgba(0,0,0,0.05);
            padding: 1rem 0;
        }
        
        .navbar-brand {
            font-weight: 800;
            font-size: 1.5rem;
            color: var(--primary) !important;
            display: flex;
            align-items: center;
        }
        
        .navbar-brand img {
            width: 32px;
            height: 32px;
            margin-right: 10px;
        }
        
        .nav-link {
            font-weight: 500;
            color: var(--dark) !important;
            margin: 0 10px;
            transition: all 0.3s;
        }
        
        .nav-link:hover {
            color: var(--primary) !important;
            transform: translateY(-2px);
        }
        
        .btn-primary {
            background: var(--primary);
            border: none;
            padding: 10px 25px;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(42,92,125,0.2);
        }
        
        .btn-outline-primary {
            border: 2px solid var(--primary);
            color: var(--primary);
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .btn-outline-primary:hover {
            background: var(--primary);
            color: white;
            transform: translateY(-2px);
        }
        
        .footer {
            background: var(--dark);
            color: white;
            padding: 60px 0 30px;
            margin-top: 80px;
        }
        
        .footer a {
            color: rgba(255,255,255,0.7);
            text-decoration: none;
            transition: all 0.3s;
        }
        
        .footer a:hover {
            color: white;
            padding-left: 5px;
        }
        
        .hero-section {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 90vh;
            display: flex;
            align-items: center;
            padding: 60px 0;
            position: relative;
            overflow: hidden;
            margin-top: -20px;
        }
        
        .hero-title {
            font-size: 3.2rem;
            font-weight: 800;
            margin-bottom: 20px;
            color: var(--dark);
        }
        
        .hero-title span {
            color: var(--primary);
            position: relative;
            display: inline-block;
        }
        
        .hero-title span::after {
            content: '';
            position: absolute;
            bottom: 10px;
            left: 0;
            width: 100%;
            height: 8px;
            background: var(--secondary);
            opacity: 0.3;
            z-index: -1;
        }
        
        .hero-subtitle {
            font-size: 1.1rem;
            color: #6c757d;
            margin-bottom: 30px;
            line-height: 1.8;
        }
        
        .stats-card {
            background: white;
            padding: 25px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            transition: all 0.3s;
            height: 100%;
        }
        
        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        }
        
        .stats-icon {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 15px;
        }
        
        .stats-icon i {
            font-size: 24px;
            color: white;
        }
        
        .stats-number {
            font-size: 2rem;
            font-weight: 800;
            color: var(--primary);
            margin-bottom: 5px;
        }
        
        .feature-card {
            background: white;
            padding: 30px 25px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            transition: all 0.3s;
            height: 100%;
            border: 1px solid rgba(0,0,0,0.05);
        }
        
        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(42,92,125,0.1);
            border-color: var(--primary);
        }
        
        .feature-icon {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
        }
        
        .feature-icon i {
            font-size: 35px;
            color: white;
        }
        
        .feature-title {
            font-size: 1.3rem;
            font-weight: 700;
            margin-bottom: 15px;
            color: var(--dark);
        }
        
        .pricing-card {
            background: white;
            border-radius: 20px;
            padding: 40px 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            transition: all 0.3s;
            height: 100%;
            position: relative;
            overflow: hidden;
        }
        
        .pricing-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(42,92,125,0.15);
        }
        
        .pricing-card.popular {
            border: 2px solid var(--primary);
            transform: scale(1.05);
        }
        
        .popular-badge {
            position: absolute;
            top: 20px;
            right: -35px;
            background: var(--primary);
            color: white;
            padding: 8px 40px;
            transform: rotate(45deg);
            font-size: 0.8rem;
            font-weight: 600;
        }
        
        .cta-section {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            padding: 60px 0;
            border-radius: 40px 40px 0 0;
            color: white;
            margin-top: 60px;
        }
        
        /* About Section Styles */
        .about-card {
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        }
        
        .contact-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
            transition: all 0.3s;
            height: 100%;
        }
        
        .contact-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(42,92,125,0.1);
        }
        
        .contact-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 24px;
        }
        
        .footer-logo-img {
            width: 24px;
            height: 24px;
            margin-right: 8px;
        }
        
        @media (max-width: 768px) {
            .hero-title {
                font-size: 2.2rem;
            }
            
            .navbar-collapse {
                background: white;
                padding: 20px;
                border-radius: 10px;
                margin-top: 10px;
                box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            }
            
            .pricing-card.popular {
                transform: scale(1);
            }
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <!-- Navigation with EasyStock Icon (using favicon) -->
    <nav class="navbar navbar-expand-lg navbar-light fixed-top">
        <div class="container">
            <a class="navbar-brand" href="{{ route('landing') }}">
                <img src="{{ asset('favicon.ico') }}" alt="EasyStock">
                EasyStock Pharmacy
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#features">Features</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#solutions">Solutions</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#about">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#contact">Contact</a>
                    </li>
                </ul>
                <div class="d-flex">
                    @auth
                        <a href="{{ route('dashboard') }}" class="btn btn-primary">
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    @yield('content')

    <!-- Footer with EasyStock Icon (using favicon) -->
    <footer class="footer">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-4">
                    <h5 class="text-white mb-3 d-flex align-items-center">
                        <img src="{{ asset('favicon.ico') }}" alt="EasyStock" class="footer-logo-img">
                        EasyStock Pharmacy
                    </h5>
                    <p class="text-white-50 small">
                        Smart inventory management solutions for modern pharmacies. 
                        Making stock management easy, efficient, and error-free since 2026.
                    </p>
                    <div class="mt-3">
                        <a href="#" class="text-white-50 me-3"><i class="fab fa-facebook fa-lg"></i></a>
                        <a href="#" class="text-white-50 me-3"><i class="fab fa-twitter fa-lg"></i></a>
                        <a href="#" class="text-white-50 me-3"><i class="fab fa-linkedin fa-lg"></i></a>
                        <a href="#" class="text-white-50"><i class="fab fa-instagram fa-lg"></i></a>
                    </div>
                </div>
                <div class="col-lg-2">
                    <h6 class="text-white mb-3">Product</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="#features" class="text-white-50 text-decoration-none small">Features</a></li>
                        <li class="mb-2"><a href="#solutions" class="text-white-50 text-decoration-none small">Solutions</a></li>
                    </ul>
                </div>
                <div class="col-lg-2">
                    <h6 class="text-white mb-3">Company</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="#about" class="text-white-50 text-decoration-none small">About Us</a></li>
                        <li class="mb-2"><a href="#contact" class="text-white-50 text-decoration-none small">Contact</a></li>
                    </ul>
                </div>
                <div class="col-lg-2">
                    <h6 class="text-white mb-3">Support</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="#" class="text-white-50 text-decoration-none small">Help Center</a></li>
                        <li class="mb-2"><a href="#" class="text-white-50 text-decoration-none small">Documentation</a></li>
                    </ul>
                </div>
                <div class="col-lg-2">
                    <h6 class="text-white mb-3">Legal</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="#" class="text-white-50 text-decoration-none small">Privacy</a></li>
                        <li class="mb-2"><a href="#" class="text-white-50 text-decoration-none small">Terms</a></li>
                    </ul>
                </div>
            </div>
            <hr class="my-4 bg-white bg-opacity-10">
            <div class="row">
                <div class="col-md-6">
                    <p class="text-white-50 small mb-0">
                        &copy; {{ date('Y') }} EasyStock Pharmacy. All rights reserved.
                    </p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="text-white-50 small mb-0">
                        <img src="{{ asset('favicon.ico') }}" alt="EasyStock" style="width: 14px; height: 14px; display: inline-block; margin-right: 4px;">
                        Made for pharmacists with <i class="fas fa-heart text-danger"></i>
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({
            duration: 800,
            once: true,
            offset: 100
        });
        
        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    </script>
    @stack('scripts')
</body>
</html>