@extends('layouts.landing')

@section('title', 'Contact Us - PharmaCare')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-lg-6" data-aos="fade-right">
            <h1 class="display-4 fw-bold mb-4">Get in Touch</h1>
            <p class="lead text-muted mb-5">
                Have questions about PharmaCare? Our team is here to help.
            </p>
            
            <div class="mb-4">
                <div class="d-flex align-items-center mb-4">
                    <div class="bg-primary bg-opacity-10 rounded-circle p-3 me-3">
                        <i class="fas fa-map-marker-alt text-primary fa-lg"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-1">Visit Us</h6>
                        <p class="text-muted mb-0">123 Health Street, Makati City, Philippines</p>
                    </div>
                </div>
                
                <div class="d-flex align-items-center mb-4">
                    <div class="bg-primary bg-opacity-10 rounded-circle p-3 me-3">
                        <i class="fas fa-phone text-primary fa-lg"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-1">Call Us</h6>
                        <p class="text-muted mb-0">(02) 8888-1234</p>
                    </div>
                </div>
                
                <div class="d-flex align-items-center mb-4">
                    <div class="bg-primary bg-opacity-10 rounded-circle p-3 me-3">
                        <i class="fas fa-envelope text-primary fa-lg"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-1">Email Us</h6>
                        <p class="text-muted mb-0">hello@pharmacare.com</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-6" data-aos="fade-left">
            <div class="card border-0 shadow-lg">
                <div class="card-body p-4">
                    <h4 class="fw-bold mb-4">Send us a message</h4>
                    <form action="#" method="POST">
                        @csrf
                        <div class="mb-3">
                            <input type="text" class="form-control form-control-lg" placeholder="Your Name" required>
                        </div>
                        <div class="mb-3">
                            <input type="email" class="form-control form-control-lg" placeholder="Email Address" required>
                        </div>
                        <div class="mb-3">
                            <input type="text" class="form-control form-control-lg" placeholder="Subject">
                        </div>
                        <div class="mb-4">
                            <textarea class="form-control form-control-lg" rows="4" placeholder="Your Message" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary btn-lg w-100">
                            <i class="fas fa-paper-plane me-2"></i>Send Message
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection