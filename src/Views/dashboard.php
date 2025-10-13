<!-- Dashboard Hero Section -->
<section class="py-5" style="margin-top: 80px;">
    <div class="container">
        <div class="row mb-5">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h1 class="display-5 mb-2" style="background: linear-gradient(135deg, var(--accent-blue), var(--accent-purple)); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
                            Welcome back, <?= htmlspecialchars($user['first_name'] ?? 'User') ?>!
                        </h1>
                        <p class="text-muted lead">Manage your personal music collection</p>
                    </div>
                    <div class="text-end d-none d-md-block">
                        <div class="text-muted small">
                            <i class="bi bi-clock me-1"></i>
                            Last login: <?= $user['last_login'] ? format_date($user['last_login'], 'M j, Y g:i A') : 'First time!' ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Stats Row -->
        <div class="row g-4 mb-5">
            <div class="col-lg-2dot4 col-md-4 col-sm-6">
                <div class="feature-card text-center">
                    <div class="stat-icon mb-3">
                        <i class="bi bi-music-note-list" style="font-size: 2.5rem; color: var(--accent-blue);"></i>
                    </div>
                    <h3 class="stat-number" style="color: var(--accent-blue);"><?= number_format($userStats['total_entries'] ?? 0) ?></h3>
                    <p class="text-muted mb-0">Total Songs</p>
                </div>
            </div>
            
            <div class="col-lg-2dot4 col-md-4 col-sm-6">
                <div class="feature-card text-center">
                    <div class="stat-icon mb-3">
                        <i class="bi bi-heart-fill" style="font-size: 2.5rem; color: var(--accent-purple);"></i>
                    </div>
                    <h3 class="stat-number" style="color: var(--accent-purple);"><?= number_format($userStats['favorite_entries'] ?? 0) ?></h3>
                    <p class="text-muted mb-0">Favorites</p>
                </div>
            </div>
            
            <div class="col-lg-2dot4 col-md-4 col-sm-6">
                <div class="feature-card text-center">
                    <div class="stat-icon mb-3">
                        <i class="bi bi-star-fill" style="font-size: 2.5rem; color: #feca57;"></i>
                    </div>
                    <h3 class="stat-number" style="color: #feca57;"><?= number_format($userStats['five_star_entries'] ?? 0) ?></h3>
                    <p class="text-muted mb-0">5-Star Rated</p>
                </div>
            </div>
            
            <div class="col-lg-2dot4 col-md-4 col-sm-6">
                <div class="feature-card text-center">
                    <div class="stat-icon mb-3">
                        <i class="bi bi-person-lines-fill" style="font-size: 2.5rem; color: #4ecdc4;"></i>
                    </div>
                    <h3 class="stat-number" style="color: #4ecdc4;"><?= number_format($userStats['unique_artists'] ?? 0) ?></h3>
                    <p class="text-muted mb-0">Artists</p>
                </div>
            </div>
            
            <div class="col-lg-2dot4 col-md-4 col-sm-6">
                <div class="feature-card text-center">
                    <div class="stat-icon mb-3">
                        <i class="bi bi-collection-play" style="font-size: 2.5rem; color: #ff6b6b;"></i>
                    </div>
                    <h3 class="stat-number" style="color: #ff6b6b;"><?= number_format($userStats['playlist_count'] ?? 0) ?></h3>
                    <p class="text-muted mb-0">Playlists</p>
                </div>
            </div>
        </div>

        <!-- Account Info Row -->
        <div class="row g-4 mb-5">
            <div class="col-12">
                <div class="feature-card">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="mb-0">Account Info</h4>
                        <a href="<?= route_url('profile') ?>" class="btn btn-sm btn-outline-glow">
                            <i class="bi bi-pencil me-1"></i>Edit Profile
                        </a>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-8">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-person me-2 text-muted"></i>
                                        <span><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-envelope me-2 text-muted"></i>
                                        <span class="text-truncate"><?= htmlspecialchars($user['email']) ?></span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-calendar me-2 text-muted"></i>
                                        <span>Joined <?= format_date($user['created_at'], 'M Y') ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <?php if (isset($userStats['average_rating']) && $userStats['average_rating'] > 0): ?>
                        <div class="col-md-4">
                            <div class="text-center">
                                <div class="text-muted small mb-1">Average Rating</div>
                                <div class="d-flex justify-content-center align-items-center">
                                    <?php 
                                    $avgRating = round($userStats['average_rating'], 1);
                                    for ($i = 1; $i <= 5; $i++): 
                                    ?>
                                        <i class="bi bi-star<?= $i <= $avgRating ? '-fill text-warning' : ' text-muted' ?> me-1"></i>
                                    <?php endfor; ?>
                                    <span class="ms-2 text-muted"><?= number_format($avgRating, 1) ?></span>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity / Getting Started -->
        <div class="row">
            <div class="col-12">
                <?php if (($userStats['total_entries'] ?? 0) == 0): ?>
                    <!-- Getting Started Section -->
                    <div class="feature-card text-center">
                        <div class="py-5">
                            <i class="bi bi-music-note-beamed display-1 mb-4" 
                               style="background: linear-gradient(135deg, var(--accent-blue), var(--accent-purple)); -webkit-background-clip: text; -webkit-text-fill-color: transparent;"></i>
                            <h3 class="mb-4">Ready to Start Your Music Journey?</h3>
                            <p class="text-muted mb-4 lead">
                                Your collection is empty, but that's about to change! Add your first song to get started.
                            </p>
                            
                            <div class="row justify-content-center">
                                <div class="col-md-8">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <div class="p-4 rounded" style="background: rgba(0, 212, 255, 0.1); border: 1px solid rgba(0, 212, 255, 0.2);">
                                                <i class="bi bi-plus-circle text-primary display-6 mb-3"></i>
                                                <h5>Add Manually</h5>
                                                <p class="text-muted small">Enter song details manually and add personal notes</p>
                                                <a href="<?= route_url('music.add') ?>" class="btn btn-glow">Get Started</a>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="p-4 rounded" style="background: rgba(138, 43, 226, 0.1); border: 1px solid rgba(138, 43, 226, 0.2);">
                                                <i class="bi bi-spotify text-success display-6 mb-3"></i>
                                                <h5>Search Spotify</h5>
                                                <p class="text-muted small">Search Spotify catalog and import track metadata</p>
                                                <a href="<?= route_url('music.add') ?>" class="btn btn-outline-glow">Search & Add</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <!-- Recent Activity Section -->
                    <div class="feature-card">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h4 class="mb-0">Recent Activity</h4>
                            <a href="<?= route_url('music.index') ?>" class="btn btn-sm btn-outline-glow">
                                View All <i class="bi bi-arrow-right ms-1"></i>
                            </a>
                        </div>
                        
                        <?php if (!empty($recentActivity)): ?>
                            <div class="activity-timeline">
                                <?php foreach ($recentActivity as $activity): ?>
                                    <div class="activity-item">
                                        <div class="activity-icon">
                                            <?php
                                            $iconClass = match($activity['type']) {
                                                'music_add' => 'bi-plus-circle text-success',
                                                'favorite' => 'bi-heart-fill text-danger',
                                                'profile_update' => 'bi-person-gear',
                                                'login' => 'bi-box-arrow-in-right text-info',
                                                default => 'bi-circle'
                                            };
                                            ?>
                                            <i class="bi <?= $iconClass ?>"></i>
                                        </div>
                                        <div class="activity-content">
                                            <div class="activity-header">
                                                <strong><?= htmlspecialchars($activity['action']) ?></strong>
                                                <small class="text-muted"><?= format_date($activity['timestamp'], 'M j, g:i A') ?></small>
                                            </div>
                                            <p class="activity-description mb-0">
                                                <?= htmlspecialchars($activity['description']) ?>
                                            </p>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-4 text-muted">
                                <i class="bi bi-clock-history display-4 mb-3"></i>
                                <p>No recent activity found</p>
                                <p class="small">Your activity will appear here as you use the app</p>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- Additional CSS for dashboard -->
<?php ob_start(); ?>
<style>
    /* Custom 5-column layout for large screens */
    @media (min-width: 992px) {
        .col-lg-2dot4 {
            flex: 0 0 auto;
            width: 20%;
        }
    }

    /* Page-specific responsive adjustments */
    @media (max-width: 768px) {
        .display-5 {
            font-size: 2rem;
        }
    }
</style>
<?php 
$additional_css = ob_get_clean();
?>