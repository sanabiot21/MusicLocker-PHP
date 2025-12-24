@extends('layouts.app')

@section('title', 'Forgot Password')

@section('content')
<!-- Forgot Password Form Section -->
<section class="py-5" style="margin-top: 100px;">
    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-lg-5 col-md-7 col-sm-9">
                <div class="feature-card">
                    <div class="text-center mb-4">
                        <i class="bi bi-key display-4 mb-3"
                           style="background: linear-gradient(135deg, var(--accent-blue), var(--accent-purple)); -webkit-background-clip: text; -webkit-text-fill-color: transparent;"></i>
                        <h1 class="h2 mb-2">{{ isset($approvedRequest) && $approvedRequest ? 'Reset Your Password' : 'Forgot Password?' }}</h1>
                        <p class="text-muted">
                            {{ isset($approvedRequest) && $approvedRequest ? 'Your reset has been approved! Set your new password below.' : 'Enter your email to request a password reset' }}
                        </p>
                    </div>

                    @if(isset($approvedRequest) && $approvedRequest)
                        <!-- Approved: Show password reset form -->
                        <form id="resetForm" method="POST" action="{{ route('password.update') }}" novalidate>
                            @csrf
                    @else
                        <!-- Not approved: Show email form -->
                        <form id="forgotForm" method="POST" action="{{ route('password.email') }}" novalidate>
                            @csrf
                    @endif

                        <!-- Display validation errors -->

                        @if(isset($approvedRequest) && $approvedRequest)
                            <!-- New Password -->
                            <div class="mb-3">
                                <label for="new_password" class="form-label form-label-dark">
                                    <i class="bi bi-lock me-1"></i>New Password
                                </label>
                                <input type="password"
                                       class="form-control form-control-dark"
                                       id="new_password" name="password"
                                       placeholder="Enter new password" required minlength="8">
                                <div class="form-text text-muted small mt-2">
                                    <i class="bi bi-info-circle me-1"></i>Minimum 8 characters
                                </div>
                            </div>

                            <!-- Confirm Password -->
                            <div class="mb-4">
                                <label for="confirm_password" class="form-label form-label-dark">
                                    <i class="bi bi-lock-fill me-1"></i>Confirm Password
                                </label>
                                <input type="password"
                                       class="form-control form-control-dark"
                                       id="confirm_password" name="password_confirmation"
                                       placeholder="Confirm new password" required minlength="8">
                            </div>

                            <input type="hidden" name="token" value="{{ $approvedRequest['token'] ?? '' }}">
                            <input type="hidden" name="email" value="{{ $approvedRequest['email'] ?? '' }}">

                            <!-- Submit Button -->
                            <button type="submit" formaction="{{ route('password.update') }}" class="btn btn-glow w-100 py-3 mb-3">
                                <i class="bi bi-key me-2"></i>Reset Password
                            </button>
                        @else
                            <!-- Email -->
                            <div class="mb-4">
                                <label for="email" class="form-label form-label-dark">
                                    <i class="bi bi-envelope me-1"></i>Email Address
                                </label>
                                <input type="email"
                                       class="form-control form-control-dark @error('email') is-invalid @enderror"
                                       id="email" name="email"
                                       value="{{ request('email', old('email')) }}"
                                       placeholder="Enter your email address" required>
                                <div class="form-text text-muted small mt-2">
                                    <i class="bi bi-info-circle me-1"></i>Enter email to check approval status or submit request
                                </div>
                            </div>

                            @if(isset($pendingMessage) && $pendingMessage)
                                <div class="alert alert-info mb-3">
                                    <i class="bi bi-hourglass-split me-2"></i>{{ $pendingMessage }}
                                </div>
                            @endif

                            <!-- Submit Button -->
                            <button type="submit" class="btn btn-glow w-100 py-3 mb-3">
                                <i class="bi bi-envelope me-2"></i>Check Status / Request Reset
                            </button>
                        @endif

                        <!-- Back to Login -->
                        <div class="text-center">
                            <a href="{{ route('login') }}" class="text-decoration-none"
                               style="color: var(--accent-blue);">
                                <i class="bi bi-arrow-left me-1"></i>Back to Login
                            </a>
                        </div>
                    </form>

                    <!-- Additional Help -->
                    <div class="text-center mt-4 pt-4 border-top" style="border-color: #333 !important;">
                        <div class="text-muted small">
                            <p class="mb-2"><strong>Having trouble?</strong></p>
                            <p class="mb-1">- Make sure you entered the correct email address</p>
                            <p class="mb-1">- Check your spam/junk folder for the reset email</p>
                            <p class="mb-3">- The reset link expires after 1 hour for security</p>
                            <p>
                                Don't have an account?
                                <a href="{{ route('register') }}" class="text-decoration-none"
                                   style="color: var(--accent-blue);">Create one here</a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
    // Form validation and submission
    document.getElementById('forgotForm')?.addEventListener('submit', function(event) {
        const form = this;
        const email = document.getElementById('email').value.trim();

        // Client-side validation
        let isValid = true;

        if (!email || !email.match(/^[^\s@]+@[^\s@]+\.[^\s@]+$/)) {
            document.getElementById('email').classList.add('is-invalid');
            isValid = false;
        } else {
            document.getElementById('email').classList.remove('is-invalid');
        }

        if (isValid) {
            // Show loading state
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;

            submitBtn.innerHTML = '<i class="bi bi-circle-notch spin me-2"></i>Sending...';
            submitBtn.disabled = true;

            // Allow form to submit naturally
        } else {
            event.preventDefault();
            event.stopPropagation();
        }

        form.classList.add('was-validated');
    });

    // Remove validation classes on input
    document.getElementById('email')?.addEventListener('input', function() {
        this.classList.remove('is-invalid');
    });
</script>
@endpush
