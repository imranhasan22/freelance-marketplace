@extends('layouts.app')

@section('content')
<!-- Hero Section -->
<section class="bg-primary text-white py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="display-4 fw-bold mb-4">Find the Perfect Freelancer for Your Project</h1>
                <p class="lead mb-4">Connect with talented professionals from around the world. Get your project done quickly and efficiently.</p>
                <div class="d-flex gap-3">
                    <a href="{{ route('jobs.create') }}" class="btn btn-light btn-lg">Post a Job</a>
                    <a href="{{ route('services.index') }}" class="btn btn-outline-light btn-lg">Browse Services</a>
                </div>
            </div>
            <div class="col-lg-6">
                <img src="/placeholder.svg?height=400&width=500" alt="Freelancer working" class="img-fluid rounded">
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="py-5">
    <div class="container">
        <div class="row text-center mb-5">
            <div class="col-lg-8 mx-auto">
                <h2 class="display-5 fw-bold mb-3">Why Choose FreelanceHub?</h2>
                <p class="lead text-muted">We make it easy to connect, collaborate, and get work done.</p>
            </div>
        </div>
        
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card h-100 text-center p-4">
                    <div class="card-body">
                        <i class="bi bi-shield-check text-primary fs-1 mb-3"></i>
                        <h5 class="card-title">Secure Payments</h5>
                        <p class="card-text">Your payments are protected with our escrow system. Pay only when you're satisfied.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 text-center p-4">
                    <div class="card-body">
                        <i class="bi bi-people text-primary fs-1 mb-3"></i>
                        <h5 class="card-title">Verified Freelancers</h5>
                        <p class="card-text">Work with pre-vetted professionals with proven track records and verified skills.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 text-center p-4">
                    <div class="card-body">
                        <i class="bi bi-clock text-primary fs-1 mb-3"></i>
                        <h5 class="card-title">24/7 Support</h5>
                        <p class="card-text">Get help whenever you need it with our round-the-clock customer support team.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Popular Categories -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row text-center mb-5">
            <div class="col-lg-8 mx-auto">
                <h2 class="display-5 fw-bold mb-3">Popular Categories</h2>
                <p class="lead text-muted">Explore services in trending categories</p>
            </div>
        </div>
        
        <div class="row g-4">
            <div class="col-md-3 col-sm-6">
                <div class="card text-center p-4 h-100">
                    <i class="bi bi-code-slash text-primary fs-1 mb-3"></i>
                    <h6 class="card-title">Web Development</h6>
                    <p class="text-muted small">1,234 services</p>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="card text-center p-4 h-100">
                    <i class="bi bi-palette text-primary fs-1 mb-3"></i>
                    <h6 class="card-title">Graphic Design</h6>
                    <p class="text-muted small">856 services</p>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="card text-center p-4 h-100">
                    <i class="bi bi-pen text-primary fs-1 mb-3"></i>
                    <h6 class="card-title">Writing & Translation</h6>
                    <p class="text-muted small">642 services</p>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="card text-center p-4 h-100">
                    <i class="bi bi-megaphone text-primary fs-1 mb-3"></i>
                    <h6 class="card-title">Digital Marketing</h6>
                    <p class="text-muted small">423 services</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-6 mb-4">
                <div class="card bg-primary text-white p-4">
                    <div class="card-body">
                        <h3 class="card-title">For Clients</h3>
                        <p class="card-text">Find the perfect freelancer for your project. Post a job and receive proposals from qualified professionals.</p>
                        <a href="{{ route('jobs.create') }}" class="btn btn-light">Post a Job</a>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card bg-success text-white p-4">
                    <div class="card-body">
                        <h3 class="card-title">For Freelancers</h3>
                        <p class="card-text">Showcase your skills and find great projects. Create your profile and start earning today.</p>
                        <a href="{{ route('register') }}" class="btn btn-light">Get Started</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
