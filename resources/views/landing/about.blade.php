@extends('layouts.landing')

@section('title', 'About Us - PharmaCare')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-lg-6" data-aos="fade-right">
            <h1 class="display-4 fw-bold mb-4">About PharmaCare</h1>
            <p class="lead text-muted mb-4">
                We're on a mission to transform pharmacy management through intelligent technology.
            </p>
            <p class="mb-4">
                Founded in 2019, PharmaCare has grown from a small startup to a trusted partner for 
                hundreds of pharmacies across the Philippines. We believe that pharmacists should 
                spend less time on paperwork and more time caring for patients.
            </p>
            <div class="row mt-5">
                <div class="col-md-6 mb-3">
                    <div class="d-flex">
                        <i class="fas fa-users text-primary fa-2x me-3"></i>
                        <div>
                            <h5>500+</h5>
                            <p class="text-muted">Active Pharmacies</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <div class="d-flex">
                        <i class="fas fa-trophy text-primary fa-2x me-3"></i>
                        <div>
                            <h5>5+ Years</h5>
                            <p class="text-muted">Industry Experience</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6" data-aos="fade-left">
            <img src="https://img.freepik.com/free-vector/about-us-concept-illustration_114360-669.jpg" 
                 alt="About Us" class="img-fluid">
        </div>
    </div>
</div>
@endsection