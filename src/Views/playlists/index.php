<!-- Playlists Index Page -->
<section class="py-5" style="margin-top: 80px;">
    <div class="container">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1><i class="bi bi-music-note-list me-2"></i>Your Playlists</h1>
            <a href="/playlists/create" class="btn btn-glow">
                <i class="bi bi-plus-circle me-2"></i>Create Playlist
            </a>
        </div>

        <?php if (empty($playlists)): ?>
            <!-- Empty State -->
            <div class="feature-card text-center py-5">
                <i class="bi bi-music-note-list display-1 text-muted mb-3"></i>
                <h3>No Playlists Yet</h3>
                <p class="text-muted mb-4">Create your first playlist to organize your music collection</p>
                <a href="/playlists/create" class="btn btn-glow">
                    <i class="bi bi-plus-circle me-2"></i>Create Your First Playlist
                </a>
            </div>
        <?php else: ?>
            <!-- Playlists Grid -->
            <div class="row g-4">
                <?php foreach ($playlists as $playlist): ?>
                    <div class="col-md-6 col-lg-4">
                        <a href="/playlists/<?= $playlist['id'] ?>" class="text-decoration-none">
                            <div class="feature-card h-100 playlist-card">
                                <div class="d-flex align-items-start mb-3">
                                    <div class="flex-grow-1">
                                        <h5 class="mb-1"><?= e($playlist['name']) ?></h5>
                                        <?php if (!empty($playlist['description'])): ?>
                                            <p class="text-muted small mb-0"><?= e(substr($playlist['description'], 0, 80)) ?><?= strlen($playlist['description']) > 80 ? '...' : '' ?></p>
                                        <?php endif; ?>
                                    </div>
                                    <?php if ($playlist['is_public']): ?>
                                        <span class="badge bg-success ms-2">
                                            <i class="bi bi-globe"></i> Public
                                        </span>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top border-secondary">
                                    <div class="small text-muted">
                                        <i class="bi bi-music-note me-1"></i><?= $playlist['track_count'] ?? 0 ?> tracks
                                    </div>
                                    <?php if (!empty($playlist['total_duration'])): ?>
                                        <div class="small text-muted">
                                            <i class="bi bi-clock me-1"></i><?= gmdate("H:i:s", $playlist['total_duration']) ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="small text-muted mt-2">
                                    <i class="bi bi-calendar me-1"></i>Updated <?= time_ago($playlist['updated_at']) ?>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

