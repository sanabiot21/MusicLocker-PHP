@extends('layouts.app')

@section('title', 'Register')

@section('content')
<!-- Registration Form Section -->
<section class="py-5" style="margin-top: 100px;">
    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-lg-6 col-md-8 col-sm-10">
                <div class="feature-card">
                    <div class="text-center mb-4">
                        <i class="bi bi-person-plus display-4 mb-3"
                           style="background: linear-gradient(135deg, var(--accent-blue), var(--accent-purple)); -webkit-background-clip: text; -webkit-text-fill-color: transparent;"></i>
                        <h1 class="h2 mb-2">Create Your Account</h1>
                        <p class="text-muted">Join Music Locker and start building your personal music collection</p>
                    </div>

                    <form id="registerForm" method="POST" action="{{ route('register') }}" novalidate>
                        @csrf

                        <div class="row">
                            <!-- First Name -->
                            <div class="col-md-6 mb-3">
                                <label for="first_name" class="form-label form-label-dark">
                                    <i class="bi bi-person me-1"></i>First Name
                                </label>
                                <input type="text"
                                       class="form-control form-control-dark @error('first_name') is-invalid @enderror"
                                       id="first_name" name="first_name"
                                       value="{{ old('first_name') }}"
                                       placeholder="Enter your first name" required>
                                @error('first_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @else
                                    <div class="invalid-feedback">
                                        Please provide your first name.
                                    </div>
                                @enderror
                            </div>

                            <!-- Last Name -->
                            <div class="col-md-6 mb-3">
                                <label for="last_name" class="form-label form-label-dark">
                                    <i class="bi bi-person me-1"></i>Last Name
                                </label>
                                <input type="text"
                                       class="form-control form-control-dark @error('last_name') is-invalid @enderror"
                                       id="last_name" name="last_name"
                                       value="{{ old('last_name') }}"
                                       placeholder="Enter your last name" required>
                                @error('last_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @else
                                    <div class="invalid-feedback">
                                        Please provide your last name.
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <!-- Email -->
                        <div class="mb-3">
                            <label for="email" class="form-label form-label-dark">
                                <i class="bi bi-envelope me-1"></i>Email Address
                            </label>
                            <input type="email"
                                   class="form-control form-control-dark @error('email') is-invalid @enderror"
                                   id="email" name="email"
                                   value="{{ old('email') }}"
                                   placeholder="Enter your email address" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @else
                                <div class="invalid-feedback">
                                    Please provide a valid email address.
                                </div>
                            @enderror
                        </div>

                        <!-- Password -->
                        <div class="mb-3">
                            <label for="password" class="form-label form-label-dark">
                                <i class="bi bi-lock me-1"></i>Password
                            </label>
                            <div class="input-group">
                                <input type="password"
                                       class="form-control form-control-dark @error('password') is-invalid @enderror"
                                       id="password" name="password"
                                       placeholder="Create a secure password" required>
                                <button class="btn btn-outline-secondary" type="button" id="togglePassword"
                                        style="border-color: #333; color: var(--text-gray);">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                            @error('password')
                                <div class="text-danger small mt-1">
                                    <i class="bi bi-exclamation-circle me-1"></i>{{ $message }}
                                </div>
                            @else
                                <div class="form-text text-muted small mt-2">
                                    <i class="bi bi-info-circle me-1"></i>Password must be at least 8 characters long
                                </div>
                            @enderror
                        </div>

                        <!-- Confirm Password -->
                        <div class="mb-4">
                            <label for="password_confirmation" class="form-label form-label-dark">
                                <i class="bi bi-lock-fill me-1"></i>Confirm Password
                            </label>
                            <div class="input-group">
                                <input type="password"
                                       class="form-control form-control-dark @error('password_confirmation') is-invalid @enderror"
                                       id="password_confirmation" name="password_confirmation"
                                       placeholder="Confirm your password" required>
                                <button class="btn btn-outline-secondary" type="button" id="toggleConfirmPassword"
                                        style="border-color: #333; color: var(--text-gray);">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                            @error('password_confirmation')
                                <div class="text-danger small mt-1">
                                    <i class="bi bi-exclamation-circle me-1"></i>{{ $message }}
                                </div>
                            @else
                                <div class="invalid-feedback">
                                    Please confirm your password.
                                </div>
                            @enderror
                        </div>

                        <!-- Terms and Privacy -->
                        <div class="mb-4">
                            <div class="form-check">
                                <input class="form-check-input @error('agreeTerms') is-invalid @enderror"
                                       type="checkbox" id="agreeTerms" name="agreeTerms" required
                                       style="accent-color: var(--accent-blue);">
                                <label class="form-check-label form-label-dark small" for="agreeTerms">
                                    I agree to the <a href="#" data-bs-toggle="modal" data-bs-target="#tosModal" class="text-decoration-none" style="color: var(--accent-blue);">Terms of Service</a>
                                    and <a href="#" data-bs-toggle="modal" data-bs-target="#privacyModal" class="text-decoration-none" style="color: var(--accent-blue);">Privacy Policy</a>
                                </label>
                                @error('agreeTerms')
                                    <div class="text-danger small mt-1">
                                        <i class="bi bi-exclamation-circle me-1"></i>{{ $message }}
                                    </div>
                                @else
                                    <div class="invalid-feedback">
                                        You must agree to the terms and conditions.
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="btn btn-glow w-100 py-3 mb-3">
                            <i class="bi bi-person-plus me-2"></i>Create Account
                        </button>
                    </form>

                    <!-- Login Link -->
                    <div class="text-center mt-4">
                        <p class="text-muted">
                            Already have an account?
                            <a href="{{ route('login') }}" class="text-decoration-none"
                               style="color: var(--accent-blue);">Sign in here</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Terms of Service Modal -->
<div class="modal fade" id="tosModal" tabindex="-1" aria-labelledby="tosModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content bg-dark text-light">
            <div class="modal-header">
                <h5 class="modal-title" id="tosModalLabel">Terms of Service</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <pre style="white-space: pre-wrap; font-family: inherit;">
@php
    $tosPath = base_path('docs/TOS-and-PP.md');
    if (file_exists($tosPath)) {
        echo htmlspecialchars(file_get_contents($tosPath));
    } else {
        echo 'Terms of Service content not found.';
    }
@endphp
                </pre>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-glow" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Privacy Policy Modal -->
<div class="modal fade" id="privacyModal" tabindex="-1" aria-labelledby="privacyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content bg-dark text-light">
            <div class="modal-header">
                <h5 class="modal-title" id="privacyModalLabel">Privacy Policy</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <pre style="white-space: pre-wrap; font-family: inherit;">
@php
    $ppPath = base_path('docs/TOS-and-PP.md');
    if (file_exists($ppPath)) {
        echo htmlspecialchars(file_get_contents($ppPath));
    } else {
        echo 'Privacy Policy content not found.';
    }
@endphp
                </pre>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-glow" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Password visibility toggles
    function setupPasswordToggle(passwordId, toggleId) {
        document.getElementById(toggleId).addEventListener('click', function() {
            const password = document.getElementById(passwordId);
            const icon = this.querySelector('i');

            if (password.type === 'password') {
                password.type = 'text';
                icon.classList.remove('bi-eye');
                icon.classList.add('bi-eye-slash');
            } else {
                password.type = 'password';
                icon.classList.remove('bi-eye-slash');
                icon.classList.add('bi-eye');
            }
        });
    }

    setupPasswordToggle('password', 'togglePassword');
    setupPasswordToggle('password_confirmation', 'toggleConfirmPassword');

    // Form validation
    document.getElementById('registerForm').addEventListener('submit', function(event) {
        const form = this;
        let isValid = true;

        // Get form values
        const firstName = document.getElementById('first_name').value.trim();
        const lastName = document.getElementById('last_name').value.trim();
        const email = document.getElementById('email').value.trim();
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('password_confirmation').value;
        const agreeTerms = document.getElementById('agreeTerms').checked;

        // Validate first name
        if (!firstName || firstName.length < 2) {
            document.getElementById('first_name').classList.add('is-invalid');
            isValid = false;
        } else {
            document.getElementById('first_name').classList.remove('is-invalid');
        }

        // Validate last name
        if (!lastName || lastName.length < 2) {
            document.getElementById('last_name').classList.add('is-invalid');
            isValid = false;
        } else {
            document.getElementById('last_name').classList.remove('is-invalid');
        }

        // Validate email
        if (!email || !email.match(/^[^\s@]+@[^\s@]+\.[^\s@]+$/)) {
            document.getElementById('email').classList.add('is-invalid');
            isValid = false;
        } else {
            document.getElementById('email').classList.remove('is-invalid');
        }

        // Validate password
        if (!password || password.length < 8) {
            document.getElementById('password').classList.add('is-invalid');
            isValid = false;
        } else {
            document.getElementById('password').classList.remove('is-invalid');
        }

        // Validate password confirmation
        if (!confirmPassword || password !== confirmPassword) {
            document.getElementById('password_confirmation').classList.add('is-invalid');
            isValid = false;
        } else {
            document.getElementById('password_confirmation').classList.remove('is-invalid');
        }

        // Validate terms agreement
        if (!agreeTerms) {
            document.getElementById('agreeTerms').classList.add('is-invalid');
            isValid = false;
        } else {
            document.getElementById('agreeTerms').classList.remove('is-invalid');
        }

        if (isValid) {
            // Show loading state
            const submitBtn = form.querySelector('button[type="submit"]');

            submitBtn.innerHTML = '<i class="bi bi-circle-notch spin me-2"></i>Creating Account...';
            submitBtn.disabled = true;

            // Allow form to submit naturally
        } else {
            event.preventDefault();
            event.stopPropagation();
        }

        form.classList.add('was-validated');
    });

    // Real-time validation feedback
    ['first_name', 'last_name', 'email', 'password', 'password_confirmation'].forEach(function(fieldId) {
        document.getElementById(fieldId).addEventListener('input', function() {
            this.classList.remove('is-invalid');

            // Special handling for password confirmation
            if (fieldId === 'password_confirmation' || fieldId === 'password') {
                const password = document.getElementById('password').value;
                const confirmPassword = document.getElementById('password_confirmation').value;

                if (confirmPassword && password !== confirmPassword) {
                    document.getElementById('password_confirmation').classList.add('is-invalid');
                } else if (confirmPassword && password === confirmPassword) {
                    document.getElementById('password_confirmation').classList.remove('is-invalid');
                }
            }
        });
    });

    // Terms checkbox validation
    document.getElementById('agreeTerms').addEventListener('change', function() {
        this.classList.remove('is-invalid');
    });
</script>
@endpush
