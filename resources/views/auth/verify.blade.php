{{-- resources/views/auth/verify.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="fas fa-envelope"></i> Verify Your Email Address</h4>
                </div>

                <div class="card-body">
                    @if (session('resent'))
                        <div class="alert alert-success" role="alert">
                            A fresh verification link has been sent to your email address.
                        </div>
                    @endif

                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="alert alert-info">
                        <p class="mb-2"><strong>Before proceeding, please check your email for a verification link.</strong></p>
                        <p class="mb-0">If you did not receive the email, click the button below to request another.</p>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-12 text-center">
                            <i class="fas fa-envelope-open-text fa-4x text-primary mb-3"></i>
                            <p>Verification email sent to: <strong>{{ auth()->user()->email }}</strong></p>
                        </div>
                    </div>

                    <div class="row mb-0">
                        <div class="col-md-6">
                            <form method="POST" action="{{ route('verification.resend') }}">
                                @csrf
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-paper-plane"></i> Resend Verification Email
                                </button>
                            </form>
                        </div>
                        <div class="col-md-6">
                            <a href="{{ route('logout') }}" 
                               onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                               class="btn btn-outline-danger w-100">
                                <i class="fas fa-sign-out-alt"></i> Logout
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection