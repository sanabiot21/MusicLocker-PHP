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
            <div class="col-md-3 col-sm-6">
                <div class="feature-card text-center">
                    <div class="stat-icon mb-3">
                        <i class="bi bi-music-note-list" style="font-size: 2.5rem; color: var(--accent-blue);"></i>
                    </div>
                    <h3 class="stat-number" style="color: var(--accent-blue);"><?= number_format($userStats['total_entries'] ?? 0) ?></h3>
                    <p class="text-muted mb-0">Total Songs</p>
                </div>
            </div>
            
            <div class="col-md-3 col-sm-6">
                <div class="feature-card text-center">
                    <div class="stat-icon mb-3">
                        <i class="bi bi-heart-fill" style="font-size: 2.5rem; color: var(--accent-purple);"></i>
                    </div>
                    <h3 class="stat-number" style="color: var(--accent-purple);"><?= number_format($userStats['favorite_entries'] ?? 0) ?></h3>
                    <p class="text-muted mb-0">Favorites</p>
                </div>
            </div>
            
            <div class="col-md-3 col-sm-6">
                <div class="feature-card text-center">
                    <div class="stat-icon mb-3">
                        <i class="bi bi-star-fill" style="font-size: 2.5rem; color: #feca57;"></i>
                    </div>
                    <h3 class="stat-number" style="color: #feca57;"><?= number_format($userStats['five_star_entries'] ?? 0) ?></h3>
                    <p class="text-muted mb-0">5-Star Rated</p>
                </div>
            </div>
            
            <div class="col-md-3 col-sm-6">
                <div class="feature-card text-center">
                    <div class="stat-icon mb-3">
                        <i class="bi bi-person-music" style="font-size: 2.5rem; color: #4ecdc4;"></i>
                    </div>
                    <h3 class="stat-number" style="color: #4ecdc4;"><?= number_format($userStats['unique_artists'] ?? 0) ?></h3>
                    <p class="text-muted mb-0">Artists</p>
                </div>
            </div>
        </div>

        <!-- Quick Actions Row -->
        <div class="row g-4 mb-5">
            <div class="col-lg-8">
                <div class="feature-card">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="mb-0">Quick Actions</h4>
                        <i class="bi bi-lightning text-warning" style="font-size: 1.5rem;"></i>
                    </div>
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <a href="<?= route_url('music.add') ?>" class="btn btn-glow w-100 py-3">
                                <i class="bi bi-plus-circle me-2"></i>Add New Song
                            </a>
                        </div>
                        <div class="col-md-6">
                            <div class="btn btn-outline-secondary w-100 py-3 d-flex align-items-center justify-content-center">
                                <i class="bi bi-spotify me-2"></i>Spotify Search Available
                                <i class="bi bi-check-circle ms-2"></i>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <a href="<?= route_url('music.index') ?>" class="btn btn-outline-secondary w-100 py-3">
                                <i class="bi bi-search me-2"></i>Browse Collection
                            </a>
                        </div>
                        <div class="col-md-6">
                            <a href="<?= route_url('music.index') ?>?favorites=1" class="btn btn-outline-secondary w-100 py-3">
                                <i class="bi bi-heart-fill me-2"></i>View Favorites
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="feature-card">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="mb-0">Account Info</h4>
                        <a href="<?= route_url('profile') ?>" class="btn btn-sm btn-outline-glow">
                            <i class="bi bi-pencil me-1"></i>Edit
                        </a>
                    </div>
                    
                    <div class="mb-3">
                        <div class="d-flex align-items-center mb-2">
                            <i class="bi bi-person me-2 text-muted"></i>
                            <span><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></span>
                        </div>
                        <div class="d-flex align-items-center mb-2">
                            <i class="bi bi-envelope me-2 text-muted"></i>
                            <span class="text-truncate"><?= htmlspecialchars($user['email']) ?></span>
                        </div>
                        <div class="d-flex align-items-center">
                            <i class="bi bi-calendar me-2 text-muted"></i>
                            <span>Joined <?= format_date($user['created_at'], 'M Y') ?></span>
                        </div>
                    </div>
                    
                    <?php if (isset($userStats['average_rating']) && $userStats['average_rating'] > 0): ?>
                    <div class="text-center pt-3 border-top" style="border-color: #333 !important;">
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
                    <?php endif; ?>
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
                        
                        <!-- Recent Music Entries -->
                        <?php if (!empty($recentEntries)): ?>
                            <div class="row g-3">
                                <?php foreach (array_slice($recentEntries, 0, 6) as $entry): ?>
                                    <div class="col-md-6 col-lg-4">
                                        <div class="music-entry-card p-3">
                                            <div class="d-flex">
                                                <?php if ($entry['album_art_url']): ?>
                                                    <img src="<?= e($entry['album_art_url']) ?>" 
                                                         alt="<?= e($entry['title']) ?>" 
                                                         class="album-art-small me-3">
                                                <?php else: ?>
                                                    <div class="album-art-small-placeholder me-3">
                                                        <i class="bi bi-music-note"></i>
                                                    </div>
                                                <?php endif; ?>
                                                <div class="flex-grow-1">
                                                    <h6 class="mb-1">
                                                        <a href="<?= route_url('music.show', $entry['id']) ?>" 
                                                           class="text-decoration-none text-light">
                                                            <?= e($entry['title']) ?>
                                                        </a>
                                                    </h6>
                                                    <p class="text-muted mb-1 small"><?= e($entry['artist']) ?></p>
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <?php if ($entry['personal_rating']): ?>
                                                            <div>
                                                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                                                    <i class="bi bi-star<?= $i <= $entry['personal_rating'] ? '-fill text-warning' : ' text-muted' ?>" style="font-size: 0.7rem;"></i>
                                                                <?php endfor; ?>
                                                            </div>
                                                        <?php else: ?>
                                                            <div></div>
                                                        <?php endif; ?>
                                                        <small class="text-muted">
                                                            <?= format_time_ago($entry['created_at']) ?>
                                                        </small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            
                            <?php if (count($recentEntries) > 6): ?>
                                <div class="text-center mt-4">
                                    <a href="<?= route_url('music.index') ?>" class="btn btn-outline-glow">
                                        View All <?= count($recentEntries) ?> Entries
                                        <i class="bi bi-arrow-right ms-1"></i>
                                    </a>
                                </div>
                            <?php endif; ?>
                        <?php else: ?>
                            <!-- Placeholder for recent activity -->
                            <div class="text-center py-4 text-muted">
                                <i class="bi bi-clock-history display-4 mb-3"></i>
                                <p>Recent activity will appear here</p>
                                <p class="small">Add some songs to see your activity timeline</p>
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
    .stat-number {
        font-size: 2.5rem;
        font-weight: 700;
        line-height: 1;
        margin-bottom: 0.5rem;
        font-family: 'Kode Mono', monospace;
    }
    
    .stat-icon {
        height: 80px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .feature-card {
        transition: transform 0.2s ease;
    }
    
    .feature-card:hover {
        transform: translateY(-2px);
    }
    
    .album-art-small {
        width: 50px;
        height: 50px;
        object-fit: cover;
        border-radius: 8px;
        border: 1px solid rgba(255, 255, 255, 0.1);
    }
    
    .album-art-small-placeholder {
        width: 50px;
        height: 50px;
        background: linear-gradient(135deg, #1a1a1a, #2a2a2a);
        border-radius: 8px;
        border: 1px solid rgba(255, 255, 255, 0.1);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--accent-blue);
    }
    
    .music-entry-card {
        transition: all 0.2s ease;
        cursor: pointer;
    }
    
    .music-entry-card:hover {
        transform: translateY(-2px);
        background: rgba(255, 255, 255, 0.05);
    }
    
    @media (max-width: 768px) {
        .stat-number {
            font-size: 2rem;
        }
        
        .display-5 {
            font-size: 2rem;
        }
        
        .album-art-small,
        .album-art-small-placeholder {
            width: 40px;
            height: 40px;
        }
    }
</style>
<?php 
$additional_css = ob_get_clean();
?>