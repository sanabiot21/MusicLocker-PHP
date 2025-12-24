@extends('layouts.app')

@section('title', 'Account Banned')

@section('content')
<!-- Banned Account Section -->
<section class="py-5" style="margin-top: 100px;">
    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-lg-6 col-md-8 col-sm-10">
                <div class="feature-card">
                    <div class="text-center mb-4">
                        <i class="bi bi-shield-exclamation display-4 mb-3 text-danger"
                           style="font-size: 4rem;"></i>
                        <h1 class="h2 mb-2">Account Banned</h1>
                        <p class="text-muted">Your account has been restricted from accessing this service.</p>
                    </div>

                    @if(session('ban_reason'))
                        <div class="alert alert-danger mb-4">
                            <h5 class="alert-heading">
                                <i class="bi bi-info-circle me-2"></i>Reason for Ban
                            </h5>
                            <p class="mb-0">{{ session('ban_reason') }}</p>
                        </div>
                    @else
                        <div class="alert alert-warning mb-4">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            Your account has been banned. Please contact the administrator for more information.
                        </div>
                    @endif

                    <div class="d-grid gap-2 mb-4">
                        <a href="{{ route('auth.account-recovery', ['email' => session('user_email')]) }}" class="btn btn-glow">
                            <i class="bi bi-envelope me-2"></i>Contact Administrator
                        </a>
                        <a href="{{ route('login') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-2"></i>Back to Login
                        </a>
                    </div>

                    <div class="text-center text-muted small">
                        <p class="mb-0">
                            If you believe this is an error, please contact the administrator to request account recovery.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection





