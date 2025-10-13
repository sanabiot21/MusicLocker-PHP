<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 hero-content">
                <h1 class="hero-title">Your Personal Music Universe</h1>
                <p class="hero-subtitle">
                    Create and manage your personal music catalog without relying on external streaming platforms. 
                    Discover, organize, and cherish your musical journey in a private, distraction-free environment.
                </p>
                <div class="d-flex flex-column flex-md-row gap-3">
                    <a href="<?= route_url('register') ?>" class="btn btn-glow btn-lg px-4 py-3">
                        <i class="bi bi-rocket-takeoff me-2"></i>Get Started
                    </a>
                    <a href="#features" class="btn btn-outline-glow btn-lg px-4 py-3">
                        <i class="bi bi-info-circle me-2"></i>Learn More
                    </a>
                </div>
            </div>
            <div class="col-lg-6 text-center">
                <div class="hero-image">
                    <div class="vinyl-logo-container">
                        <img src="<?= asset_url('img/vinyl-record.svg') ?>" alt="Music Locker Vinyl Logo" class="vinyl-logo">
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section id="features" class="py-5">
    <div class="container">
        <div class="row mb-5">
            <div class="col-lg-8 mx-auto text-center">
                <h2 class="display-4 mb-4" style="background: linear-gradient(135deg, var(--accent-blue), var(--accent-purple)); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
                    Why Choose Music Locker?
                </h2>
                <p class="lead text-muted">
                    Built for music enthusiasts who want to maintain a personal record of their musical journey
                </p>
            </div>
        </div>
        
        <div class="row g-4">
            <div class="col-md-6 col-lg-4">
                <div class="feature-card text-center">
                    <div class="feature-icon">
                        <i class="bi bi-shield-lock"></i>
                    </div>
                    <h4 class="feature-title">Private & Secure</h4>
                    <p class="feature-description">
                        Your music catalog is completely private. No algorithms, no external interference - 
                        just your personal musical space.
                    </p>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-4">
                <div class="feature-card text-center">
                    <div class="feature-icon">
                        <i class="bi bi-collection"></i>
                    </div>
                    <h4 class="feature-title">Organize Your Collection</h4>
                    <p class="feature-description">
                        Add songs, albums, and create custom mood tags. Keep personal notes and memories 
                        associated with your favorite tracks.
                    </p>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-4">
                <div class="feature-card text-center">
                    <div class="feature-icon">
                        <i class="bi bi-search"></i>
                    </div>
                    <h4 class="feature-title">Smart Discovery</h4>
                    <p class="feature-description">
                        Search and filter your collection by artist, album, genre, or mood. 
                        Find the perfect song for any moment.
                    </p>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-4">
                <div class="feature-card text-center">
                    <div class="feature-icon">
                        <i class="bi bi-tags"></i>
                    </div>
                    <h4 class="feature-title">Custom Mood Tags</h4>
                    <p class="feature-description">
                        Create personalized tags to categorize music by mood, occasion, or any 
                        criteria that matters to you.
                    </p>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-4">
                <div class="feature-card text-center">
                    <div class="feature-icon">
                        <i class="bi bi-phone"></i>
                    </div>
                    <h4 class="feature-title">Mobile Responsive</h4>
                    <p class="feature-description">
                        Access your music collection from any device. Responsive design ensures 
                        a seamless experience across all screen sizes.
                    </p>
                </div>
            </div>
            
            
        </div>
    </div>
</section>

<!-- Call to Action Section -->
<section class="py-5 text-center bg-gradient-section">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <h2 class="display-5 mb-4" style="color: var(--accent-blue);">
                    Ready to Start Your Musical Journey?
                </h2>
                <p class="lead text-muted mb-4">
                    Join thousands of music lovers who have discovered the joy of organizing their personal music collections.
                </p>
                <div class="d-flex flex-column flex-sm-row gap-3 justify-content-center">
                    <a href="<?= route_url('register') ?>" class="btn btn-glow btn-lg px-5">
                        <i class="bi bi-person-plus me-2"></i>Create Free Account
                    </a>
                    <a href="<?= route_url('login') ?>" class="btn btn-outline-glow btn-lg px-5">
                        <i class="bi bi-box-arrow-in-right me-2"></i>Sign In
                    </a>
                </div>
                
                <div class="mt-4 text-muted small">
                    <i class="bi bi-shield-check me-2"></i>Free forever • No credit card required • Privacy guaranteed
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Statistics Section -->
<section class="py-5">
    <div class="container">
        <div class="row text-center g-4">
            <div class="col-md-3 col-sm-6">
                <div class="stat-item">
                    <div class="stat-number" style="color: var(--accent-blue);"><?php echo number_format($stats['active_users'] ?? 0); ?></div>
                    <div class="stat-label text-muted">Active Users</div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="stat-item">
                    <div class="stat-number" style="color: var(--accent-purple);\"><?php echo number_format($stats['songs_cataloged'] ?? 0); ?></div>
                    <div class="stat-label text-muted">Songs Cataloged</div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="stat-item">
                    <div class="stat-number" style="color: var(--accent-blue);\"><?php echo number_format($stats['personal_notes'] ?? 0); ?></div>
                    <div class="stat-label text-muted">Personal Notes</div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="stat-item">
                    <div class="stat-number" style="color: var(--accent-purple);\"><?php echo number_format($stats['mood_tags_created'] ?? 0); ?></div>
                    <div class="stat-label text-muted">Mood Tags Created</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Additional CSS for home page -->
<?php ob_start(); ?>
<style>
    /* Vinyl Logo Container with Glowing Border */
    .hero-image {
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        z-index: 2;
    }

    .vinyl-logo-container {
        position: relative;
        animation: vinyl-rotate 60s linear infinite;
        width: 350px;
        height: 350px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .vinyl-logo {
        width: 100%;
        height: 100%;
        display: block;
        position: relative;
        z-index: 2;
        filter: drop-shadow(0 0 25px rgba(0, 212, 255, 0.5));
    }

    .vinyl-logo-container::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: calc(100% + 20px);
        height: calc(100% + 20px);
        border-radius: 50%;
        padding: 4px;
        background: linear-gradient(135deg, var(--accent-blue) 0%, var(--accent-purple) 50%, var(--accent-blue) 100%);
        -webkit-mask:
            linear-gradient(#fff 0 0) content-box,
            linear-gradient(#fff 0 0);
        -webkit-mask-composite: xor;
        mask-composite: exclude;
        animation: border-glow 3s ease-in-out infinite, gradient-rotate 8s linear infinite;
        z-index: 1;
    }

    .vinyl-logo-container::after {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: calc(100% + 40px);
        height: calc(100% + 40px);
        border-radius: 50%;
        background: radial-gradient(circle, var(--accent-blue) 0%, var(--accent-purple) 50%, transparent 70%);
        opacity: 0.4;
        filter: blur(30px);
        animation: pulse-glow 2.5s ease-in-out infinite;
        z-index: 0;
    }

    @keyframes border-glow {
        0%, 100% {
            box-shadow: 0 0 30px rgba(0, 212, 255, 0.6),
                        0 0 60px rgba(138, 43, 226, 0.4),
                        inset 0 0 20px rgba(0, 212, 255, 0.2);
        }
        50% {
            box-shadow: 0 0 40px rgba(138, 43, 226, 0.6),
                        0 0 70px rgba(0, 212, 255, 0.4),
                        inset 0 0 30px rgba(138, 43, 226, 0.2);
        }
    }

    @keyframes pulse-glow {
        0%, 100% {
            opacity: 0.3;
            transform: scale(0.95);
        }
        50% {
            opacity: 0.5;
            transform: scale(1.05);
        }
    }

    @keyframes vinyl-rotate {
        from {
            transform: rotate(0deg);
        }
        to {
            transform: rotate(360deg);
        }
    }

    @keyframes gradient-rotate {
        0% {
            background: linear-gradient(0deg, var(--accent-blue) 0%, var(--accent-purple) 50%, var(--accent-blue) 100%);
        }
        25% {
            background: linear-gradient(90deg, var(--accent-blue) 0%, var(--accent-purple) 50%, var(--accent-blue) 100%);
        }
        50% {
            background: linear-gradient(180deg, var(--accent-blue) 0%, var(--accent-purple) 50%, var(--accent-blue) 100%);
        }
        75% {
            background: linear-gradient(270deg, var(--accent-blue) 0%, var(--accent-purple) 50%, var(--accent-blue) 100%);
        }
        100% {
            background: linear-gradient(360deg, var(--accent-blue) 0%, var(--accent-purple) 50%, var(--accent-blue) 100%);
        }
    }

    /* Pause rotation on hover */
    .vinyl-logo-container:hover {
        animation-play-state: paused;
    }

    @media (max-width: 992px) {
        .vinyl-logo-container {
            width: 300px;
            height: 300px;
        }
    }

    @media (max-width: 768px) {
        .vinyl-logo-container {
            width: 280px;
            height: 280px;
        }
    }

    @media (max-width: 576px) {
        .vinyl-logo-container {
            width: 240px;
            height: 240px;
        }
    }

    .bg-gradient-section {
        background: linear-gradient(135deg, rgba(0, 212, 255, 0.1) 0%, rgba(138, 43, 226, 0.1) 100%);
        border-top: 1px solid rgba(0, 212, 255, 0.2);
        border-bottom: 1px solid rgba(138, 43, 226, 0.2);
    }

    /* Page-specific stat number size for home hero */
    .stat-number {
        font-size: 3rem;
    }

    .stat-label {
        font-size: 1rem;
        letter-spacing: 1px;
    }

    .feature-icon {
        font-size: 3rem;
        margin-bottom: 1.5rem;
        background: linear-gradient(135deg, var(--accent-blue), var(--accent-purple));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .feature-title {
        color: var(--text-light);
        margin-bottom: 1rem;
        font-weight: 600;
    }

    .feature-description {
        color: var(--text-gray);
        line-height: 1.6;
    }
    
    .hero-section {
        padding: 8rem 0 6rem;
        min-height: 100vh;
        display: flex;
        align-items: center;
    }

    @media (max-width: 991px) {
        .hero-section {
            padding: 6rem 0 4rem;
            min-height: auto;
        }

        .hero-content {
            margin-bottom: 3rem;
        }
    }

    @media (max-width: 768px) {
        .hero-section {
            padding: 5rem 0 3rem;
        }

        .hero-content {
            margin-bottom: 2.5rem;
        }

        .stat-number {
            font-size: 2rem;
        }
    }

    @media (max-width: 576px) {
        .hero-content {
            margin-bottom: 2rem;
        }
    }
</style>
<?php 
$additional_css = ob_get_clean();
?>