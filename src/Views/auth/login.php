<!-- Login Form Section -->
<section class="py-5" style="margin-top: 100px;">
    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-lg-5 col-md-7 col-sm-9">
                <div class="feature-card">
                    <div class="text-center mb-4">
                        <i class="bi bi-box-arrow-in-right display-4 mb-3" 
                           style="background: linear-gradient(135deg, var(--accent-blue), var(--accent-purple)); -webkit-background-clip: text; -webkit-text-fill-color: transparent;"></i>
                        <h1 class="h2 mb-2">Welcome Back</h1>
                        <p class="text-muted">Sign in to access your personal music collection</p>
                    </div>

                    <form id="loginForm" method="POST" action="<?= route_url('login') ?>" novalidate>
                        <?= csrf_field() ?>
                        
                        <!-- Display validation errors -->
                        <?php if (isset($flash_messages['validation_errors'])): ?>
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    <?php foreach ($flash_messages['validation_errors'] as $field => $error): ?>
                                        <li><?= htmlspecialchars($error) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>

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
                                <div class="text-danger small mt-1">
                                    <i class="bi bi-exclamation-circle me-1"></i><?= htmlspecialchars($flash_messages['validation_errors']['email']) ?>
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
                                       placeholder="Enter your password" required>
                                <button class="btn btn-outline-secondary" type="button" id="togglePassword"
                                        style="border-color: #333; color: var(--text-gray);">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                            <?php if (isset($flash_messages['validation_errors']['password'])): ?>
                                <div class="text-danger small mt-1">
                                    <i class="bi bi-exclamation-circle me-1"></i><?= htmlspecialchars($flash_messages['validation_errors']['password']) ?>
                                </div>
                            <?php else: ?>
                                <div class="invalid-feedback">
                                    Please enter your password.
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Remember Me & Forgot Password -->
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="rememberMe" name="rememberMe"
                                       style="accent-color: var(--accent-blue);">
                                <label class="form-check-label form-label-dark" for="rememberMe">
                                    Remember me
                                </label>
                            </div>
                            <a href="<?= route_url('forgot') ?>" class="text-decoration-none small" 
                               style="color: var(--accent-blue);">
                                Forgot Password?
                            </a>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="btn btn-glow w-100 py-3 mb-3">
                            <i class="bi bi-box-arrow-in-right me-2"></i>Sign In
                        </button>

                        
                    </form>

                    <!-- Register Link -->
                    <div class="text-center mt-4">
                        <p class="text-muted">
                            Don't have an account? 
                            <a href="<?= route_url('register') ?>" class="text-decoration-none" 
                               style="color: var(--accent-blue);">Create one here</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Custom JavaScript for login form -->
<?php ob_start(); ?>
<script>
    // Password visibility toggle
    document.getElementById('togglePassword').addEventListener('click', function() {
        const password = document.getElementById('password');
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

    // Form validation and submission
    document.getElementById('loginForm').addEventListener('submit', function(event) {
        const form = this;
        const email = document.getElementById('email').value;
        const password = document.getElementById('password').value;

        // Client-side validation
        let isValid = true;

        if (!email || !email.match(/^[^\s@]+@[^\s@]+\.[^\s@]+$/)) {
            document.getElementById('email').classList.add('is-invalid');
            isValid = false;
        } else {
            document.getElementById('email').classList.remove('is-invalid');
        }

        if (!password) {
            document.getElementById('password').classList.add('is-invalid');
            isValid = false;
        } else {
            document.getElementById('password').classList.remove('is-invalid');
        }

        if (isValid) {
            // Show loading state
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            
            submitBtn.innerHTML = '<i class="bi bi-circle-notch spin me-2"></i>Signing In...';
            submitBtn.disabled = true;
            
            // Allow form to submit naturally
            // The server will handle the actual authentication
        } else {
            event.preventDefault();
            event.stopPropagation();
        }

        form.classList.add('was-validated');
    });

    // Remove validation classes on input
    ['email', 'password'].forEach(function(fieldId) {
        document.getElementById(fieldId).addEventListener('input', function() {
            this.classList.remove('is-invalid');
        });
    });
</script>
<?php 
$additional_js = ob_get_clean();
?>