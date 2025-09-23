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

                    <form id="registerForm" method="POST" action="<?= route_url('register') ?>" novalidate>
                        <?= csrf_field() ?>
                        
                        <!-- Display validation errors -->
                        <?php if (isset($flash_messages['validation_errors'])): ?>
                            <div class="alert alert-danger">
                                <h6 class="mb-2"><i class="bi bi-exclamation-triangle me-2"></i>Please correct the following errors:</h6>
                                <ul class="mb-0">
                                    <?php foreach ($flash_messages['validation_errors'] as $field => $error): ?>
                                        <li><?= htmlspecialchars($error) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <div class="row">
                            <!-- First Name -->
                            <div class="col-md-6 mb-3">
                                <label for="first_name" class="form-label form-label-dark">
                                    <i class="bi bi-person me-1"></i>First Name
                                </label>
                                <input type="text" 
                                       class="form-control form-control-dark <?= isset($flash_messages['validation_errors']['first_name']) ? 'is-invalid' : '' ?>" 
                                       id="first_name" name="first_name" 
                                       value="<?= htmlspecialchars($flash_messages['old_input']['first_name'] ?? '') ?>"
                                       placeholder="Enter your first name" required>
                                <?php if (isset($flash_messages['validation_errors']['first_name'])): ?>
                                    <div class="invalid-feedback">
                                        <?= htmlspecialchars($flash_messages['validation_errors']['first_name']) ?>
                                    </div>
                                <?php else: ?>
                                    <div class="invalid-feedback">
                                        Please provide your first name.
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Last Name -->
                            <div class="col-md-6 mb-3">
                                <label for="last_name" class="form-label form-label-dark">
                                    <i class="bi bi-person me-1"></i>Last Name
                                </label>
                                <input type="text" 
                                       class="form-control form-control-dark <?= isset($flash_messages['validation_errors']['last_name']) ? 'is-invalid' : '' ?>" 
                                       id="last_name" name="last_name" 
                                       value="<?= htmlspecialchars($flash_messages['old_input']['last_name'] ?? '') ?>"
                                       placeholder="Enter your last name" required>
                                <?php if (isset($flash_messages['validation_errors']['last_name'])): ?>
                                    <div class="invalid-feedback">
                                        <?= htmlspecialchars($flash_messages['validation_errors']['last_name']) ?>
                                    </div>
                                <?php else: ?>
                                    <div class="invalid-feedback">
                                        Please provide your last name.
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Email -->
                        <div class="mb-3">
                            <label for="email" class="form-label form-label-dark">
                                <i class="bi bi-envelope me-1"></i>Email Address
                            </label>
                            <input type="email" 
                                   class="form-control form-control-dark <?= isset($flash_messages['validation_errors']['email']) ? 'is-invalid' : '' ?>" 
                                   id="email" name="email" 
                                   value="<?= htmlspecialchars($flash_messages['old_input']['email'] ?? '') ?>"
                                   placeholder="Enter your email address" required>
                            <?php if (isset($flash_messages['validation_errors']['email'])): ?>
                                <div class="invalid-feedback">
                                    <?= htmlspecialchars($flash_messages['validation_errors']['email']) ?>
                                </div>
                            <?php else: ?>
                                <div class="invalid-feedback">
                                    Please provide a valid email address.
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Password -->
                        <div class="mb-3">
                            <label for="password" class="form-label form-label-dark">
                                <i class="bi bi-lock me-1"></i>Password
                            </label>
                            <div class="input-group">
                                <input type="password" 
                                       class="form-control form-control-dark <?= isset($flash_messages['validation_errors']['password']) ? 'is-invalid' : '' ?>" 
                                       id="password" name="password" 
                                       placeholder="Create a secure password" required>
                                <button class="btn btn-outline-secondary" type="button" id="togglePassword"
                                        style="border-color: #333; color: var(--text-gray);">
                                    <i class="bi bi-eye"></i>
                                </button>
                                <?php if (isset($flash_messages['validation_errors']['password'])): ?>
                                    <div class="invalid-feedback">
                                        <?= htmlspecialchars($flash_messages['validation_errors']['password']) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <?php if (!isset($flash_messages['validation_errors']['password'])): ?>
                                <div class="invalid-feedback">
                                    Password must be at least 8 characters long.
                                </div>
                            <?php endif; ?>
                            <div class="form-text text-muted small mt-2">
                                <i class="bi bi-info-circle me-1"></i>Password must be at least 8 characters long
                            </div>
                        </div>

                        <!-- Confirm Password -->
                        <div class="mb-4">
                            <label for="confirm_password" class="form-label form-label-dark">
                                <i class="bi bi-lock-fill me-1"></i>Confirm Password
                            </label>
                            <div class="input-group">
                                <input type="password" 
                                       class="form-control form-control-dark <?= isset($flash_messages['validation_errors']['confirm_password']) ? 'is-invalid' : '' ?>" 
                                       id="confirm_password" name="confirm_password" 
                                       placeholder="Confirm your password" required>
                                <button class="btn btn-outline-secondary" type="button" id="toggleConfirmPassword"
                                        style="border-color: #333; color: var(--text-gray);">
                                    <i class="bi bi-eye"></i>
                                </button>
                                <?php if (isset($flash_messages['validation_errors']['confirm_password'])): ?>
                                    <div class="invalid-feedback">
                                        <?= htmlspecialchars($flash_messages['validation_errors']['confirm_password']) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <?php if (!isset($flash_messages['validation_errors']['confirm_password'])): ?>
                                <div class="invalid-feedback">
                                    Please confirm your password.
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Terms and Privacy -->
                        <div class="mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="agreeTerms" name="agreeTerms" required
                                       style="accent-color: var(--accent-blue);">
                                <label class="form-check-label form-label-dark small" for="agreeTerms">
                                    I agree to the <a href="#" class="text-decoration-none" style="color: var(--accent-blue);">Terms of Service</a> 
                                    and <a href="#" class="text-decoration-none" style="color: var(--accent-blue);">Privacy Policy</a>
                                </label>
                                <div class="invalid-feedback">
                                    You must agree to the terms and conditions.
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="btn btn-glow w-100 py-3 mb-3">
                            <i class="bi bi-person-plus me-2"></i>Create Account
                        </button>

                        <!-- Divider -->
                        <div class="text-center mb-3">
                            <hr class="my-3" style="border-color: #333;">
                            <span class="text-muted small bg-dark px-3">or</span>
                        </div>

                        <!-- Social Registration Buttons (Placeholder) -->
                        <div class="d-grid gap-2">
                            <button type="button" class="btn btn-outline-glow py-2" disabled>
                                <i class="bi bi-google me-2"></i>Continue with Google
                            </button>
                            <button type="button" class="btn btn-outline-glow py-2" disabled>
                                <i class="bi bi-github me-2"></i>Continue with GitHub
                            </button>
                        </div>
                        
                        <div class="text-center mt-2">
                            <small class="text-muted">Social registration coming soon!</small>
                        </div>
                    </form>

                    <!-- Login Link -->
                    <div class="text-center mt-4">
                        <p class="text-muted">
                            Already have an account? 
                            <a href="<?= route_url('login') ?>" class="text-decoration-none" 
                               style="color: var(--accent-blue);">Sign in here</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Custom JavaScript for registration form -->
<?php ob_start(); ?>
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
    setupPasswordToggle('confirm_password', 'toggleConfirmPassword');

    // Form validation
    document.getElementById('registerForm').addEventListener('submit', function(event) {
        const form = this;
        let isValid = true;

        // Get form values
        const firstName = document.getElementById('first_name').value.trim();
        const lastName = document.getElementById('last_name').value.trim();
        const email = document.getElementById('email').value.trim();
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('confirm_password').value;
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
            document.getElementById('confirm_password').classList.add('is-invalid');
            isValid = false;
        } else {
            document.getElementById('confirm_password').classList.remove('is-invalid');
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
            const originalText = submitBtn.innerHTML;
            
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
    ['first_name', 'last_name', 'email', 'password', 'confirm_password'].forEach(function(fieldId) {
        document.getElementById(fieldId).addEventListener('input', function() {
            this.classList.remove('is-invalid');
            
            // Special handling for password confirmation
            if (fieldId === 'confirm_password' || fieldId === 'password') {
                const password = document.getElementById('password').value;
                const confirmPassword = document.getElementById('confirm_password').value;
                
                if (confirmPassword && password !== confirmPassword) {
                    document.getElementById('confirm_password').classList.add('is-invalid');
                } else if (confirmPassword && password === confirmPassword) {
                    document.getElementById('confirm_password').classList.remove('is-invalid');
                }
            }
        });
    });

    // Terms checkbox validation
    document.getElementById('agreeTerms').addEventListener('change', function() {
        this.classList.remove('is-invalid');
    });
</script>
<?php 
$additional_js = ob_get_clean();
?>