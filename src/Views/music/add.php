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

                <!-- Add Form -->
                <div class="feature-card">
                    <!-- Header -->
                    <div class="mb-4 pb-4 border-bottom">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0">
                                <i class="bi bi-pencil me-2"></i>Add Music Entry
                            </h5>
                            <button type="button" class="btn btn-outline-glow" data-bs-toggle="modal" data-bs-target="#searchModal">
                                <i class="bi bi-search me-2"></i>Search Online
                            </button>
                        </div>
                        <p class="text-muted small mb-0">
                            <i class="bi bi-info-circle me-1"></i>Search online to auto-fill track details, or enter information manually below.
                        </p>
                    </div>
                    
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
                                    <label for="genre" class="form-label">Genre *</label>
                                    <input type="text" class="form-control" id="genre" name="genre" value="Unknown" required>
                                </div>
                                <div class="col-md-3">
                                    <label for="release_year" class="form-label">Year</label>
                                    <input type="number" class="form-control" id="release_year" name="release_year" 
                                           min="1900" max="<?= date('Y') ?>">
                                </div>
                                
                                <div class="col-md-4">
                                    <label for="personal_rating" class="form-label">Personal Rating *</label>
                                    <select class="form-select" id="personal_rating" name="personal_rating" required>
                                        <option value="">Select Rating</option>
                                        <option value="1">1 Star</option>
                                        <option value="2">2 Stars</option>
                                        <option value="3" selected>3 Stars</option>
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
                                
                                <!-- Tags Selection -->
                                <?php if (!empty($availableTags)): ?>
                                    <div class="col-12">
                                        <label class="form-label">
                                            <i class="bi bi-tags me-1"></i>Tags (Optional)
                                        </label>
                                        <div class="d-flex flex-wrap gap-2">
                                            <?php foreach ($availableTags as $tag): ?>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="tags[]" 
                                                           value="<?= $tag['id'] ?>" id="tag_<?= $tag['id'] ?>">
                                                    <label class="form-check-label" for="tag_<?= $tag['id'] ?>">
                                                        <span class="badge" style="background-color: <?= e($tag['color']) ?>;">
                                                            <?= e($tag['name']) ?>
                                                        </span>
                                                    </label>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                
                                <!-- Hidden Spotify fields -->
                                <input type="hidden" id="spotify_id" name="spotify_id">
                                <input type="hidden" id="spotify_url" name="spotify_url">
                                <input type="hidden" id="album_art_url" name="album_art_url">
                                
                                <!-- Personal Notes Section -->
                                <div class="col-12">
                                    <hr class="my-4">
                                    <h6 class="mb-3"><i class="bi bi-pencil-square me-2"></i>Personal Notes (Optional)</h6>
                                </div>
                                
                                <div class="col-12">
                                    <label for="note_text" class="form-label">Notes & Thoughts</label>
                                    <textarea class="form-control" id="note_text" name="note_text" rows="3" 
                                              placeholder="Your thoughts, memories, or why you love this song..."></textarea>
                                </div>
                                
                                <div class="col-md-4">
                                    <label for="mood" class="form-label">Mood</label>
                                    <input type="text" class="form-control" id="mood" name="mood" 
                                           placeholder="e.g., Happy, Nostalgic">
                                </div>
                                
                                <div class="col-md-4">
                                    <label for="memory_context" class="form-label">Memory Context</label>
                                    <input type="text" class="form-control" id="memory_context" name="memory_context" 
                                           placeholder="e.g., Summer 2020">
                                </div>
                                
                                <div class="col-md-4">
                                    <label for="listening_context" class="form-label">Listening Context</label>
                                    <input type="text" class="form-control" id="listening_context" name="listening_context" 
                                           placeholder="e.g., Workout, Study">
                                </div>
                                
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
</section>

<!-- Music Search Modal -->
<div class="modal fade" id="searchModal" tabindex="-1" aria-labelledby="searchModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content bg-dark">
            <div class="modal-header border-bottom border-secondary">
                <h5 class="modal-title" id="searchModalLabel">
                    <i class="bi bi-search me-2"></i>Search Music Database
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Search Form -->
                <form method="GET" id="spotifySearchForm" class="mb-4">
                    <div class="input-group input-group-lg">
                        <input type="text" class="form-control" name="q" id="searchInput" 
                               placeholder="Search for songs or albums..." 
                               value="<?= e($searchQuery) ?>" autofocus required>
                        <button type="submit" class="btn btn-glow" id="searchSubmitBtn">
                            <i class="bi bi-search" id="searchIcon"></i>
                            <span id="searchText" class="d-none d-md-inline ms-2">Search</span>
                        </button>
                    </div>
                    <small class="text-muted d-block mt-2">
                        <i class="bi bi-info-circle me-1"></i>Search by track name, artist, or album title
                    </small>
                </form>

                <!-- Loading State -->
                <div id="searchLoading" class="text-center py-5 d-none">
                    <div class="spinner-border text-primary mb-3" role="status" style="width: 3rem; height: 3rem;">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="text-muted">Searching music database...</p>
                </div>

                <!-- Search Results -->
                <div id="searchResults">
                    <?php if (!empty($albumTracks) || (!empty($spotifyResults) && !empty($spotifyResults['tracks']['items']))): ?>
                        
                        <!-- Album Tracks (if found) -->
                        <?php if (!empty($albumTracks) && !empty($albumInfo)): ?>
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="mb-0">
                                    <i class="bi bi-disc me-2"></i>Tracks in "<?= e($albumInfo['name']) ?>"
                                </h6>
                                <span class="badge bg-primary"><?= count($albumTracks) ?> tracks</span>
                            </div>
                            <small class="text-muted d-block mb-3">
                                By <?= e(implode(', ', $albumInfo['artists'])) ?>
                                <?php if (!empty($albumInfo['release_date'])): ?>
                                    • <?= e(substr($albumInfo['release_date'], 0, 4)) ?>
                                <?php endif; ?>
                            </small>
                            <div class="row g-2 mb-4">
                                <?php foreach ($albumTracks as $track): ?>
                                    <div class="col-12">
                                        <div class="card bg-secondary track-card" style="transition: all 0.2s;">
                                            <div class="row g-0">
                                                <div class="col-auto" style="width: 80px;">
                                                    <?php if (!empty($albumInfo['images'][0]['url'])): ?>
                                                        <img src="<?= e($albumInfo['images'][0]['url']) ?>" 
                                                             class="img-fluid rounded-start" 
                                                             style="width: 80px; height: 80px; object-fit: cover;" 
                                                             alt="<?= e($albumInfo['name']) ?>">
                                                    <?php else: ?>
                                                        <div class="bg-dark d-flex align-items-center justify-content-center rounded-start" style="width: 80px; height: 80px;">
                                                            <i class="bi bi-disc fs-4 text-muted"></i>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="col">
                                                    <div class="card-body p-2 d-flex align-items-center">
                                                        <div class="flex-grow-1 me-2" style="min-width: 0;">
                                                            <h6 class="card-title mb-0 text-truncate"><?= e($track['name']) ?></h6>
                                                            <p class="card-text text-muted small mb-0">
                                                                <span class="text-muted">#<?= (int)$track['track_number'] ?></span>
                                                                • <?= e($track['artist_names']) ?>
                                                                <?php if (!empty($track['duration_ms'])): ?>
                                                                    • <?= gmdate('i:s', (int)($track['duration_ms'] / 1000)) ?>
                                                                <?php endif; ?>
                                                            </p>
                                                        </div>
                                                        <div>
                                                            <button type="button" class="btn btn-glow btn-sm select-track" 
                                                                    data-track='<?= json_encode([
                                                                        'title' => $track['name'],
                                                                        'artist' => $track['artist_names'],
                                                                        'album' => $albumInfo['name'],
                                                                        'release_year' => !empty($albumInfo['release_date']) ? substr($albumInfo['release_date'], 0, 4) : '',
                                                                        'duration' => !empty($track['duration_ms']) ? (int)round($track['duration_ms'] / 1000) : 0,
                                                                        'spotify_id' => $track['id'],
                                                                        'spotify_url' => $track['spotify_url'] ?? '',
                                                                        'album_art_url' => !empty($albumInfo['images'][0]['url']) ? $albumInfo['images'][0]['url'] : '',
                                                                        'genre' => $track['genre'] ?? 'Unknown'
                                                                    ]) ?>'>
                                                                <i class="bi bi-check-circle"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <!-- Other Track Results -->
                        <?php if (!empty($spotifyResults['tracks']['items'])): ?>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="mb-0">
                                <i class="bi bi-music-note me-2"></i>Tracks
                        </h6>
                            <span class="badge bg-secondary"><?= count($spotifyResults['tracks']['items']) ?> found</span>
                        </div>
                        <div class="row g-2 mb-4">
                            <?php foreach ($spotifyResults['tracks']['items'] as $track): ?>
                                <div class="col-12">
                                    <div class="card bg-secondary track-card" style="transition: all 0.2s;">
                                        <div class="row g-0">
                                            <div class="col-auto" style="width: 80px;">
                                                <?php if (!empty($track['album']['images'][0]['url'])): ?>
                                                    <img src="<?= e($track['album']['images'][0]['url']) ?>" 
                                                         class="img-fluid rounded-start" 
                                                         style="width: 80px; height: 80px; object-fit: cover;" 
                                                         alt="<?= e($track['name']) ?>">
                                                <?php else: ?>
                                                    <div class="bg-dark d-flex align-items-center justify-content-center rounded-start" style="width: 80px; height: 80px;">
                                                        <i class="bi bi-music-note fs-4 text-muted"></i>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            <div class="col">
                                                <div class="card-body p-2 d-flex align-items-center">
                                                    <div class="flex-grow-1 me-2" style="min-width: 0;">
                                                        <h6 class="card-title mb-0 text-truncate"><?= e($track['name']) ?></h6>
                                                        <p class="card-text text-muted small mb-0 text-truncate">
                                                            <?= e(implode(', ', array_column($track['artists'], 'name'))) ?>
                                                            <?php if (!empty($track['album']['name'])): ?>
                                                                • <?= e($track['album']['name']) ?>
                                                            <?php endif; ?>
                                                        </p>
                                                        <?php if (!empty($track['genre'])): ?>
                                                            <span class="badge bg-dark mt-1"><?= e($track['genre']) ?></span>
                                                    <?php endif; ?>
                                                    </div>
                                                    <div>
                                                        <button type="button" class="btn btn-glow btn-sm select-track" 
                                                                data-track='<?= json_encode([
                                                                    'title' => $track['name'],
                                                                    'artist' => implode(', ', array_column($track['artists'], 'name')),
                                                                    'album' => $track['album']['name'],
                                                                    'release_year' => !empty($track['album']['release_date']) ? substr($track['album']['release_date'], 0, 4) : '',
                                                                    'duration' => round($track['duration_ms'] / 1000),
                                                                    'spotify_id' => $track['id'],
                                                                    'spotify_url' => $track['external_urls']['spotify'] ?? '',
                                                                    'album_art_url' => $track['album']['images'][0]['url'] ?? '',
                                                                    'genre' => $track['genre'] ?? 'Unknown'
                                                                ]) ?>'>
                                                            <i class="bi bi-check-circle"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                        
                    <?php elseif (!empty($searchQuery)): ?>
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>No results found for "<strong><?= e($searchQuery) ?></strong>". Try a different search term.
                        </div>
                    <?php else: ?>
                        <div class="text-center text-muted py-5">
                            <i class="bi bi-search display-1 mb-3"></i>
                            <p>Enter a song, artist, or album name to search</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i>Close
                </button>
            </div>
        </div>
    </div>
</div>

<?php ob_start(); ?>
<script src="/assets/js/music-add.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Loading indicator for search form
    const searchForm = document.getElementById('spotifySearchForm');
    const searchBtn = document.getElementById('searchSubmitBtn');
    const searchIcon = document.getElementById('searchIcon');
    const searchLoading = document.getElementById('searchLoading');
    const searchResults = document.getElementById('searchResults');
    
    if (searchForm) {
        searchForm.addEventListener('submit', function() {
            // Show loading state
            searchBtn.disabled = true;
            searchIcon.className = 'bi bi-arrow-repeat';
            searchIcon.style.animation = 'spin 1s linear infinite';
            
            // Hide results, show loading
            if (searchResults) searchResults.classList.add('d-none');
            if (searchLoading) searchLoading.classList.remove('d-none');
        });
    }
    
    // Auto-open modal if there are search results
    <?php if (!empty($searchQuery)): ?>
    const searchModal = new bootstrap.Modal(document.getElementById('searchModal'));
    searchModal.show();
    <?php endif; ?>
});

// Add spin animation
const style = document.createElement('style');
style.textContent = `
    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
`;
document.head.appendChild(style);
</script>
<?php 
$additional_js = ob_get_clean();
?>