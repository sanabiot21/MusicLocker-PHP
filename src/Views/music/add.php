<!-- Add Music Page Content -->
<section class="py-5" style="margin-top: 80px;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <!-- Header -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1><i class="bi bi-plus-circle me-2"></i>Add Music</h1>
                    <a href="<?= route_url('music') ?>" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i>Back to Collection
                    </a>
                </div>

                <!-- Flash Messages -->
                <?php if ($message = flash('success')): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle me-2"></i><?= e($message) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <?php if ($message = flash('error')): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-triangle me-2"></i><?= e($message) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Search Section -->
                <div class="mb-4">
                    <form method="GET" class="row g-3">
                        <div class="col-md-8">
                            <input type="text" class="form-control" name="q" placeholder="Search for songs, artists, or albums..." 
                                   value="<?= e($searchQuery) ?>" autofocus>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-glow w-100">
                                <i class="bi bi-search me-2"></i>Search
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Search Results -->
                <?php if (!empty($spotifyResults) && !empty($spotifyResults['tracks']['items'])): ?>
                    <div class="feature-card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="bi bi-music-note me-2"></i>Search Results 
                                <small class="text-muted">(<?= count($spotifyResults['tracks']['items']) ?> tracks)</small>
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <?php foreach ($spotifyResults['tracks']['items'] as $track): ?>
                                    <div class="col-md-6 mb-3">
                                        <div class="card bg-secondary">
                                            <div class="row g-0">
                                                <div class="col-4">
                                                    <?php if (!empty($track['album']['images'][0]['url'])): ?>
                                                        <img src="<?= e($track['album']['images'][0]['url']) ?>" 
                                                             class="img-fluid rounded-start h-100" 
                                                             style="object-fit: cover; min-height: 120px;" 
                                                             alt="<?= e($track['name']) ?>">
                                                    <?php else: ?>
                                                        <div class="bg-dark d-flex align-items-center justify-content-center rounded-start h-100" 
                                                             style="min-height: 120px;">
                                                            <i class="bi bi-music-note display-6 text-muted"></i>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="col-8">
                                                    <div class="card-body p-3">
                                                        <h6 class="card-title mb-1"><?= e($track['name']) ?></h6>
                                                        <p class="card-text text-muted small mb-1">
                                                            <?= e(implode(', ', array_column($track['artists'], 'name'))) ?>
                                                        </p>
                                                        <p class="card-text text-muted small mb-2">
                                                            <i class="bi bi-disc me-1"></i><?= e($track['album']['name']) ?>
                                                        </p>
                                                        <button class="btn btn-glow btn-sm select-track" 
                                                                data-track='<?= json_encode([
                                                                    'title' => $track['name'],
                                                                    'artist' => implode(', ', array_column($track['artists'], 'name')),
                                                                    'album' => $track['album']['name'],
                                                                    'release_year' => !empty($track['album']['release_date']) ? substr($track['album']['release_date'], 0, 4) : '',
                                                                    'duration' => round($track['duration_ms'] / 1000),
                                                                    'spotify_id' => $track['id'],
                                                                    'spotify_url' => $track['external_urls']['spotify'] ?? '',
                                                                    'album_art_url' => $track['album']['images'][0]['url'] ?? '',
                                                                    'genre' => $track['album']['genres'][0] ?? ''
                                                                ]) ?>'>
                                                            <i class="bi bi-plus me-1"></i>Select
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                <?php elseif (!empty($searchQuery)): ?>
                    <div class="alert alert-info mb-4">
                        <i class="bi bi-info-circle me-2"></i>No results found for "<?= e($searchQuery) ?>". Try a different search term or add manually below.
                    </div>
                <?php endif; ?>

                <!-- Manual Add Form -->
                <div class="feature-card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="bi bi-pencil me-2"></i>Add Music Details
                        </h5>
                    </div>
                    <div class="card-body">
                            <form method="POST" id="addMusicForm">
                                <?= csrf_field() ?>
                            
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="title" class="form-label">Track Title *</label>
                                    <input type="text" class="form-control" id="title" name="title" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="artist" class="form-label">Artist *</label>
                                    <input type="text" class="form-control" id="artist" name="artist" required>
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="album" class="form-label">Album</label>
                                    <input type="text" class="form-control" id="album" name="album">
                                </div>
                                <div class="col-md-3">
                                    <label for="genre" class="form-label">Genre</label>
                                    <input type="text" class="form-control" id="genre" name="genre">
                                </div>
                                <div class="col-md-3">
                                    <label for="release_year" class="form-label">Year</label>
                                    <input type="number" class="form-control" id="release_year" name="release_year" 
                                           min="1900" max="<?= date('Y') ?>">
                                </div>
                                
                                <div class="col-md-4">
                                    <label for="personal_rating" class="form-label">Personal Rating</label>
                                    <select class="form-select" id="personal_rating" name="personal_rating">
                                        <option value="">Not Rated</option>
                                        <option value="1">1 Star</option>
                                        <option value="2">2 Stars</option>
                                        <option value="3">3 Stars</option>
                                        <option value="4">4 Stars</option>
                                        <option value="5">5 Stars</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="duration" class="form-label">Duration (seconds)</label>
                                    <input type="number" class="form-control" id="duration" name="duration" min="1">
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check form-switch mt-4">
                                        <input class="form-check-input" type="checkbox" id="is_favorite" name="is_favorite">
                                        <label class="form-check-label" for="is_favorite">
                                            <i class="bi bi-heart me-1"></i>Add to Favorites
                                        </label>
                                    </div>
                                </div>
                                
                                <!-- Hidden Spotify fields -->
                                <input type="hidden" id="spotify_id" name="spotify_id">
                                <input type="hidden" id="spotify_url" name="spotify_url">
                                <input type="hidden" id="album_art_url" name="album_art_url">
                                
                                <div class="col-12">
                                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                        <a href="<?= route_url('music') ?>" class="btn btn-outline-secondary me-md-2">
                                            <i class="bi bi-x-circle me-1"></i>Cancel
                                        </a>
                                        <button type="submit" class="btn btn-glow">
                                            <i class="bi bi-plus-circle me-2"></i>Add to Collection
                                        </button>
                                    </div>
                                </div>
                            </div>
                            </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

            </div>
        </div>
    </div>
</section>

<?php ob_start(); ?>
<script src="/assets/js/music-add.js"></script>
<?php 
$additional_js = ob_get_clean();
?>