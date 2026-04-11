@extends('layouts.landing')


@section('content')
<!-- Hero Section with Integrated Login -->
<section class="hero-section">
    <div class="container">
        <div class="row align-items-start">
            <!-- Left Column - Value Proposition (Fixed - No Movement) -->
                            <div class="col-lg-7" data-aos="fade-right">
                                <div class="pe-lg-5">
                                   
                                    <h1 class="display-4 fw-bold mb-4">
                                        <span class="text-primary">Smart</span> Pharmacy<br>
                                        Inventory Management
                                    </h1>
                                    <p class="lead text-secondary mb-4">
                                        Stop worrying about stockouts and expired medicines. EasyStock helps you 
                                        track inventory, manage sales, and make data-driven decisions - all in one place.
                                    </p>
                                    
                                    
                                    <!-- Key Benefits -->
                                    <div class="row g-3">
                                        <div class="col-6">
                                            <div class="d-flex">
                                                <div class="flex-shrink-0">
                                                    <i class="fas fa-check-circle text-primary me-2"></i>
                                                </div>
                                                <div>
                                                    <h6 class="fw-bold mb-0">Real-time Tracking</h6>
                                                    <small class="text-muted">Know your stock instantly</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="d-flex">
                                                <div class="flex-shrink-0">
                                                    <i class="fas fa-check-circle text-primary me-2"></i>
                                                </div>
                                                <div>
                                                    <h6 class="fw-bold mb-0">Expiry Alerts</h6>
                                                    <small class="text-muted">Never lose products</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="d-flex">
                                                <div class="flex-shrink-0">
                                                    <i class="fas fa-check-circle text-primary me-2"></i>
                                                </div>
                                                <div>
                                                    <h6 class="fw-bold mb-0">Smart Reports</h6>
                                                    <small class="text-muted">Data-driven decisions</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="d-flex">
                                                <div class="flex-shrink-0">
                                                    <i class="fas fa-check-circle text-primary me-2"></i>
                                                </div>
                                                <div>
                                                    <h6 class="fw-bold mb-0">POS Integration</h6>
                                                    <small class="text-muted">Seamless checkout</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Right Column - Login/Register Form (Fixed Height) -->
                            <div class="col-lg-5" data-aos="fade-left">
                                <div class="card border-0 shadow-lg rounded-4" style="min-height: 550px;">
                                    <div class="card-header bg-white border-0 pt-4 pb-0 px-4">
                                        <ul class="nav nav-pills nav-justified mb-3" id="pills-tab" role="tablist">
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link active fw-semibold" id="pills-login-tab" data-bs-toggle="pill" data-bs-target="#pills-login" type="button" role="tab" aria-controls="pills-login" aria-selected="true">
                                                    <i class="fas fa-sign-in-alt me-2"></i>Login
                                                </button>
                                            </li>
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link fw-semibold" id="pills-register-tab" data-bs-toggle="pill" data-bs-target="#pills-register" type="button" role="tab" aria-controls="pills-register" aria-selected="false">
                                                    <i class="fas fa-user-plus me-2"></i>Register
                                                </button>
                                            </li>
                                        </ul>
                                    </div>
                                    
                                    <div class="card-body p-4">
                                        <div class="tab-content" id="pills-tabContent">
                                           <!-- Login Tab -->
<div class="tab-pane fade show active" id="pills-login" role="tabpanel" aria-labelledby="pills-login-tab">

    {{-- ✅ Registration success --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- ✅ Newly registered email pre-fill notice --}}
    @if(session('registered_email'))
        <div class="alert alert-info alert-dismissible fade show mb-3" role="alert">
            <i class="fas fa-info-circle me-2"></i>
            Account created for: <strong>{{ session('registered_email') }}</strong>. You can now login.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- ❌ Email not found --}}
    @if(session('error_type') === 'email')
        <div class="alert alert-warning alert-dismissible fade show mb-3" role="alert">
            <i class="fas fa-user-times me-2"></i>
            <strong>Email not found.</strong> No account is registered with that email address.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- ❌ Wrong password --}}
    @if(session('error_type') === 'password')
        <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
            <i class="fas fa-lock me-2"></i>
            <strong>Incorrect password.</strong> Please double-check your password and try again.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf
        
        <div class="mb-3">
            <label for="email" class="form-label fw-semibold small text-uppercase text-muted">Email Address</label>
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0">
                    <i class="fas fa-envelope text-primary"></i>
                </span>
                <input type="email" 
                    class="form-control border-start-0 {{ session('error_type') === 'email' ? 'is-invalid border-warning' : '' }}" 
                    id="email" 
                    name="email" 
                    value="{{ old('email', session('registered_email')) }}" 
                    placeholder="your@email.com"
                    required>
            </div>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label fw-semibold small text-uppercase text-muted">Password</label>
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0">
                    <i class="fas fa-lock text-primary"></i>
                </span>
                <input type="password" 
                    class="form-control border-start-0 {{ session('error_type') === 'password' ? 'is-invalid' : '' }}" 
                    id="password" 
                    name="password" 
                    placeholder="••••••••"
                    required>
                <button class="btn btn-outline-secondary border-start-0" type="button" id="togglePassword">
                    <i class="fas fa-eye"></i>
                </button>
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                <label class="form-check-label small" for="remember">Remember me</label>
            </div>
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="small text-decoration-none">Forgot password?</a>
            @endif
        </div>

        <button type="submit" class="btn btn-primary w-100 py-2 mb-4">
            <i class="fas fa-sign-in-alt me-2"></i>Sign In
        </button>
        
        <!-- Simple Chatbot Description -->
        <div class="text-center mt-3 pt-2 border-top">
            <div class="d-flex align-items-center justify-content-center mb-2">
                <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill">
                    <i class="fas fa-robot me-2"></i>Meet EasyStock AI Assistant
                </span>
            </div>
            <p class="small text-muted mb-2">
                Your 24/7 pharmacy assistant that answers medicine queries, 
                checks stock levels, and provides instant sales reports.
            </p>
            <div class="d-flex justify-content-center gap-3">
                <div class="text-center">
                    <i class="fas fa-check-circle text-success fa-sm"></i>
                    <small class="text-muted ms-1">Medicine info</small>
                </div>
                <div class="text-center">
                    <i class="fas fa-check-circle text-success fa-sm"></i>
                    <small class="text-muted ms-1">Stock alerts</small>
                </div>
                <div class="text-center">
                    <i class="fas fa-check-circle text-success fa-sm"></i>
                    <small class="text-muted ms-1">Sales reports</small>
                </div>
            </div>
        </div>
    </form>
</div>
                            <!-- Register Tab -->
<div class="tab-pane fade" id="pills-register" role="tabpanel" aria-labelledby="pills-register-tab">
    <!-- Display validation errors -->
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            <strong>Please fix the following errors:</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form method="POST" action="{{ route('register') }}">
        @csrf
        
        <div class="mb-3">
            <label for="reg_name" class="form-label fw-semibold small text-uppercase text-muted">Full Name <span class="text-danger">*</span></label>
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0">
                    <i class="fas fa-user text-primary"></i>
                </span>
                <input type="text" 
                       class="form-control border-start-0 @error('name') is-invalid @enderror" 
                       id="reg_name" 
                       name="name" 
                       value="{{ old('name') }}" 
                       placeholder="John Doe"
                       required>
            </div>
            @error('name')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        <div class="mb-3">
            <label for="reg_email" class="form-label fw-semibold small text-uppercase text-muted">Email Address <span class="text-danger">*</span></label>
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0">
                    <i class="fas fa-envelope text-primary"></i>
                </span>
                <input type="email" 
                       class="form-control border-start-0 @error('email') is-invalid @enderror" 
                       id="reg_email" 
                       name="email" 
                       value="{{ old('email') }}" 
                       placeholder="your@email.com"
                       required>
            </div>
            @error('email')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="reg_password" class="form-label fw-semibold small text-uppercase text-muted">Password <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0">
                        <i class="fas fa-lock text-primary"></i>
                    </span>
                    <input type="password" 
                           class="form-control border-start-0 @error('password') is-invalid @enderror" 
                           id="reg_password" 
                           name="password" 
                           placeholder="••••••••"
                           required>
                </div>
                @error('password')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <div class="col-md-6 mb-3">
                <label for="password_confirmation" class="form-label fw-semibold small text-uppercase text-muted">Confirm Password <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0">
                        <i class="fas fa-lock text-primary"></i>
                    </span>
                    <input type="password" 
                           class="form-control border-start-0" 
                           id="password_confirmation" 
                           name="password_confirmation" 
                           placeholder="••••••••"
                           required>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="reg_phone" class="form-label fw-semibold small text-uppercase text-muted">Phone Number</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0">
                        <i class="fas fa-phone text-primary"></i>
                    </span>
                    <input type="text" 
                           class="form-control border-start-0" 
                           id="reg_phone" 
                           name="phone" 
                           value="{{ old('phone') }}" 
                           placeholder="+63 XXX XXX XXXX">
                </div>
            </div>

            <div class="col-md-6 mb-3">
                <label for="reg_address" class="form-label fw-semibold small text-uppercase text-muted">Address <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0">
                        <i class="fas fa-map-marker-alt text-primary"></i>
                    </span>
                    <input type="text" 
                           class="form-control border-start-0 @error('address') is-invalid @enderror" 
                           id="reg_address" 
                           name="address" 
                           value="{{ old('address') }}" 
                           placeholder="Enter your address"
                           required>
                </div>
                @error('address')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
        </div>

        <div class="mb-4">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="terms" name="terms" required>
                <label class="form-check-label small" for="terms">
                    I agree to the <a href="#" class="text-decoration-none">Terms of Service</a> and <a href="#" class="text-decoration-none">Privacy Policy</a>
                </label>
            </div>
        </div>

        <button type="submit" class="btn btn-success w-100 py-2">
            <i class="fas fa-user-plus me-2"></i>Create Account
        </button>
        
        <!-- Link back to login -->
        <div class="text-center mt-3">
            <p class="small text-muted mb-0">
                Already have an account? 
                <a href="#" class="text-decoration-none" onclick="document.getElementById('pills-login-tab').click(); return false;">
                    Sign in here
                </a>
            </p>
        </div>
    </form>
</div>
                    
                    <!-- Social Login (Optional) -->
                    
                
                
    </div>
</section>

<!-- Features Section -->
<section id="features" class="py-5">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <span class="badge bg-primary bg-opacity-10 text-primary px-4 py-2 rounded-pill mb-3">
                <i class="fas fa-crown me-2"></i>Features
            </span>
            <h2 class="display-5 fw-bold mb-3">Everything You Need in One System</h2>
            <p class="lead text-muted col-lg-8 mx-auto">
                Powerful features designed specifically for pharmacies of all sizes
            </p>
        </div>
        
        <div class="row g-4">
            <!-- Feature 1 -->
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-boxes"></i>
                    </div>
                    <h3 class="feature-title">Smart Inventory</h3>
                    <p class="text-muted">
                        Real-time stock tracking, automated reordering, expiry date monitoring, and low stock alerts. 
                        Never run out of essential medicines again.
                    </p>
                    <ul class="list-unstyled mt-3">
                        <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i>Batch tracking</li>
                        <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i>Expiry notifications</li>
                        <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i>Reorder automation</li>
                    </ul>
                </div>
            </div>
            
            <!-- Feature 2 -->
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="200">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-robot"></i>
                    </div>
                    <h3 class="feature-title">AI Assistant</h3>
                    <p class="text-muted">
                        24/7 intelligent chatbot that answers medicine queries, checks stock, provides prices, and generates instant reports.
                    </p>
                    <ul class="list-unstyled mt-3">
                        <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i>"How many Paracetamol?"</li>
                        <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i>"Show expired medicines"</li>
                        <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i>"Today's sales"</li>
                    </ul>
                </div>
            </div>
            
            <!-- Feature 3 -->
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="300">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-chart-pie"></i>
                    </div>
                    <h3 class="feature-title">Advanced Analytics</h3>
                    <p class="text-muted">
                        Comprehensive reports on sales trends, top-selling products, profit margins, and customer behavior patterns.
                    </p>
                    <ul class="list-unstyled mt-3">
                        <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i>Sales forecasting</li>
                        <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i>Profit analysis</li>
                        <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i>Inventory turnover</li>
                    </ul>
                </div>
            </div>
            
            <!-- Feature 4 -->
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="400">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-cash-register"></i>
                    </div>
                    <h3 class="feature-title">Point of Sale</h3>
                    <p class="text-muted">
                        Fast, intuitive POS system with barcode scanning, prescription validation, and multiple payment options.
                    </p>
                    <ul class="list-unstyled mt-3">
                        <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i>Barcode scanner</li>
                        <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i>Digital receipts</li>
                        <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i>Offline mode</li>
                    </ul>
                </div>
            </div>
            
            <!-- Feature 5 -->
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="500">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-truck"></i>
                    </div>
                    <h3 class="feature-title">Supplier Management</h3>
                    <p class="text-muted">
                        Streamlined communication with suppliers, purchase order management, and automated reordering.
                    </p>
                    <ul class="list-unstyled mt-3">
                        <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i>Supplier database</li>
                        <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i>Price comparison</li>
                        <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i>Order tracking</li>
                    </ul>
                </div>
            </div>
            
            <!-- Feature 6 -->
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="600">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h3 class="feature-title">Compliance Ready</h3>
                    <p class="text-muted">
                        Built-in compliance with FDA regulations, prescription tracking, and secure audit trails.
                    </p>
                    <ul class="list-unstyled mt-3">
                        <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i>Audit logs</li>
                        <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i>Prescription control</li>
                        <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i>GDPR ready</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Solutions Section -->
<section id="solutions" class="py-5 bg-light">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6" data-aos="fade-right">
                <span class="badge bg-secondary bg-opacity-10 text-secondary px-4 py-2 rounded-pill mb-3">
                    <i class="fas fa-store me-2"></i>Solutions
                </span>
                <h2 class="display-5 fw-bold mb-3">Built for Every Type of Pharmacy</h2>
                <p class="lead text-muted mb-4">
                    Whether you're a small independent pharmacy or a large chain, EasyStock adapts to your needs.
                </p>
                
                <div class="row mt-4">
                    <div class="col-md-6 mb-3">
                        <div class="d-flex">
                            <div class="flex-shrink-0">
                                <div class="bg-primary bg-opacity-10 rounded-circle p-2">
                                    <i class="fas fa-store-alt text-primary"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="fw-bold">Retail Pharmacy</h6>
                                <p class="text-muted small">Perfect for community pharmacies and drugstores.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="d-flex">
                            <div class="flex-shrink-0">
                                <div class="bg-primary bg-opacity-10 rounded-circle p-2">
                                    <i class="fas fa-hospital text-primary"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="fw-bold">Hospital Pharmacy</h6>
                                <p class="text-muted small">Manage large inventories across departments.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="d-flex">
                            <div class="flex-shrink-0">
                                <div class="bg-primary bg-opacity-10 rounded-circle p-2">
                                    <i class="fas fa-clinic-medical text-primary"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="fw-bold">Clinic Pharmacy</h6>
                                <p class="text-muted small">Streamlined for medical clinics and practices.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="d-flex">
                            <div class="flex-shrink-0">
                                <div class="bg-primary bg-opacity-10 rounded-circle p-2">
                                    <i class="fas fa-warehouse text-primary"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="fw-bold">Pharmacy Chains</h6>
                                <p class="text-muted small">Multi-branch management with central control.</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                @auth
                    <a href="{{ route('dashboard') }}" class="btn btn-primary mt-3 px-4 py-2">
                        <i class="fas fa-tachometer-alt me-2"></i>Go to Dashboard
                    </a>
                @endauth
            </div>
            <div class="col-lg-6" data-aos="fade-left">
                <img src="https://img.freepik.com/free-vector/online-pharmacy-abstract-concept-illustration_335657-3947.jpg" 
                     alt="Pharmacy Solutions" 
                     class="img-fluid rounded-4 shadow">
            </div>
        </div>
    </div>
</section>

<!-- About Section -->
<section id="about" class="py-5">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <span class="badge bg-primary bg-opacity-10 text-primary px-4 py-2 rounded-pill mb-3">
                <i class="fas fa-info-circle me-2"></i>About Us
            </span>
            <h2 class="display-5 fw-bold mb-3">EasyStock Pharmacy</h2>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-8 text-center" data-aos="fade-up">
                <p class="lead mb-4">
                    EasyStock Pharmacy is your trusted partner in pharmacy inventory management, 
                    dedicated to helping pharmacies across the Philippines operate more efficiently.
                </p>
                <p class="text-muted">
                    Located in the heart of Bohol, we understand the unique challenges faced by 
                    local pharmacies and have designed our system to address those specific needs.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Contact Section -->
<section id="contact" class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <span class="badge bg-primary bg-opacity-10 text-primary px-4 py-2 rounded-pill mb-3">
                <i class="fas fa-headset me-2"></i>Contact Us
            </span>
            <h2 class="display-5 fw-bold mb-3">Get in Touch</h2>
            <p class="lead text-muted col-lg-8 mx-auto">
                We're here to help! Reach out to us through any of these channels.
            </p>
        </div>

        <div class="row g-4">
            <!-- Address Card -->
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center p-4">
                        <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex p-3 mb-3">
                            <i class="fas fa-map-marker-alt text-primary fa-2x"></i>
                        </div>
                        <h5 class="fw-bold mb-3">Address</h5>
                        <p class="text-muted mb-0">
                            <strong>Panaban Poblacion</strong><br>
                            Trinidad, Bohol
                        </p>
                    </div>
                </div>
            </div>

            <!-- Contact Number Card -->
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center p-4">
                        <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex p-3 mb-3">
                            <i class="fas fa-phone text-success fa-2x"></i>
                        </div>
                        <h5 class="fw-bold mb-3">Contact Number</h5>
                        <p class="text-muted mb-0">
                            <strong>0912 126 7889</strong>
                        </p>
                        <small class="text-muted">Call or text us</small>
                    </div>
                </div>
            </div>

            <!-- Email Card -->
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center p-4">
                        <div class="bg-warning bg-opacity-10 rounded-circle d-inline-flex p-3 mb-3">
                            <i class="fas fa-envelope text-warning fa-2x"></i>
                        </div>
                        <h5 class="fw-bold mb-3">Email</h5>
                        <p class="text-muted mb-0">
                            <strong>EasyStock Pharmacy</strong>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Business Hours -->
        <div class="row mt-4" data-aos="fade-up">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <div class="row align-items-center">
                            <div class="col-md-3 text-center text-md-start mb-3 mb-md-0">
                                <div class="bg-info bg-opacity-10 rounded-circle d-inline-flex p-3">
                                    <i class="fas fa-clock text-info fa-2x"></i>
                                </div>
                            </div>
                            <div class="col-md-9">
                                <h5 class="fw-bold mb-3">Business Hours</h5>
                                <div class="row">
                                    <div class="col-sm-4">
                                        <p class="mb-1"><strong>Monday - Friday:</strong></p>
                                        <p class="text-muted">8:00 AM - 8:00 PM</p>
                                    </div>
                                    <div class="col-sm-4">
                                        <p class="mb-1"><strong>Saturday:</strong></p>
                                        <p class="text-muted">9:00 AM - 6:00 PM</p>
                                    </div>
                                    <div class="col-sm-4">
                                        <p class="mb-1"><strong>Sunday:</strong></p>
                                        <p class="text-muted">10:00 AM - 4:00 PM</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


@push('scripts')
<script>
    // Toggle password visibility
    document.getElementById('togglePassword')?.addEventListener('click', function() {
        const password = document.getElementById('password');
        const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
        password.setAttribute('type', type);
        this.querySelector('i').classList.toggle('fa-eye');
        this.querySelector('i').classList.toggle('fa-eye-slash');
    });

    // Auto-switch to login tab if there are errors or registered email
    document.addEventListener('DOMContentLoaded', function() {
        // Check if we should show the login tab
        @if(session('registered_email') || session('show_login') || $errors->any() || session('error'))
            var loginTab = new bootstrap.Tab(document.getElementById('pills-login-tab'));
            loginTab.show();
        @endif

        // Auto-switch to register tab if there are registration errors with name
        @if($errors->any() && old('name'))
            var registerTab = new bootstrap.Tab(document.getElementById('pills-register-tab'));
            registerTab.show();
        @endif
    });
</script>
@endpush

@endsection