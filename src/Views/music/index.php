<!-- Music Collection Page Content -->
<section class="py-5" style="margin-top: 80px;">
    <div class="container">
        <!-- Header & Actions -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h1 class="mb-0">
                        <i class="bi bi-collection me-2"></i>My Music Collection
                        <?php if (isset($stats)): ?>
                            <small class="text-muted ms-2">(<?= $stats['total_entries'] ?? 0 ?> tracks)</small>
                        <?php endif; ?>
                    </h1>
                    <a href="<?= route_url('music.add') ?>" class="btn btn-glow">
                        <i class="bi bi-plus-circle me-2"></i>Add Music
                    </a>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="feature-card mb-4">
            <form method="GET" action="<?= route_url('music') ?>" id="musicFilterForm">
                <!-- Primary Search Row -->
                <div class="row g-2 mb-3">
                    <div class="col-md-8 col-lg-9">
                        <div class="input-group">
                            <span class="input-group-text bg-dark border-secondary">
                                <i class="bi bi-search"></i>
                            </span>
                            <input type="text" class="form-control" name="search" 
                                   placeholder="Search by title, artist, album, or tags..." 
                                   value="<?= e($_GET['search'] ?? '') ?>">
                        </div>
                    </div>
                    <div class="col-md-4 col-lg-3">
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-outline-glow flex-grow-1" id="toggleAdvancedFilters">
                                <i class="bi bi-funnel me-1"></i>Filters
                                <?php 
                                $activeFiltersCount = 0;
                                if (!empty($_GET['genre'])) $activeFiltersCount++;
                                if (!empty($_GET['tag'])) $activeFiltersCount++;
                                if (!empty($_GET['mood'])) $activeFiltersCount++;
                                if (!empty($_GET['rating'])) $activeFiltersCount++;
                                if (!empty($_GET['sort_by']) && $_GET['sort_by'] !== 'created_at') $activeFiltersCount++;
                                if ($activeFiltersCount > 0): ?>
                                    <span class="badge bg-glow"><?= $activeFiltersCount ?></span>
                                <?php endif; ?>
                            </button>
                            <button type="submit" class="btn btn-glow">
                                <i class="bi bi-search"></i>
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Advanced Filters (Collapsible) -->
                <div id="advancedFilters" class="<?= ($activeFiltersCount > 0) ? '' : 'collapse' ?>">
                    <div class="row g-2 mb-3 pb-3 border-bottom border-secondary">
                    <div class="col-md-3">
                            <label class="form-label small text-muted mb-1">Genre</label>
                            <select class="form-select form-select-sm" name="genre">
                                <option value="">All Genres</option>
                                <?php foreach ($genres as $genre): ?>
                                    <option value="<?= e($genre) ?>" <?= ($_GET['genre'] ?? '') === $genre ? 'selected' : '' ?>>
                                        <?= e($genre) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    <div class="col-md-3">
                        <label class="form-label small text-muted mb-1">Mood</label>
                        <select class="form-select form-select-sm" name="mood">
                            <option value="">All Moods</option>
                            <?php if (!empty($moodTags)): ?>
                                <?php foreach ($moodTags as $tag): ?>
                                    <option value="<?= e($tag['id']) ?>" <?= ($_GET['mood'] ?? '') == $tag['id'] ? 'selected' : '' ?>>
                                        <?= e(preg_replace('/^Mood:\s*/i', '', $tag['name'])) ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                        <div class="col-md-3">
                            <label class="form-label small text-muted mb-1">Tag</label>
                            <select class="form-select form-select-sm" name="tag">
                                <option value="">All Tags</option>
                                <?php if (!empty($availableTags)): ?>
                                    <?php foreach ($availableTags as $tag): ?>
                                        <option value="<?= e($tag['id']) ?>" <?= ($_GET['tag'] ?? '') == $tag['id'] ? 'selected' : '' ?>>
                                            <?= e($tag['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small text-muted mb-1">Rating</label>
                            <select class="form-select form-select-sm" name="rating">
                                <option value="">All Ratings</option>
                                <option value="5" <?= ($_GET['rating'] ?? '') === '5' ? 'selected' : '' ?>>⭐⭐⭐⭐⭐ (5 stars)</option>
                                <option value="4" <?= ($_GET['rating'] ?? '') === '4' ? 'selected' : '' ?>>⭐⭐⭐⭐+ (4+ stars)</option>
                                <option value="3" <?= ($_GET['rating'] ?? '') === '3' ? 'selected' : '' ?>>⭐⭐⭐+ (3+ stars)</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small text-muted mb-1">Sort By</label>
                            <select class="form-select form-select-sm" name="sort_by">
                                <option value="date_added" <?= ($_GET['sort_by'] ?? 'date_added') === 'date_added' ? 'selected' : '' ?>>Recently Added</option>
                                <option value="title" <?= ($_GET['sort_by'] ?? '') === 'title' ? 'selected' : '' ?>>Title (A-Z)</option>
                                <option value="artist" <?= ($_GET['sort_by'] ?? '') === 'artist' ? 'selected' : '' ?>>Artist (A-Z)</option>
                                <option value="personal_rating" <?= ($_GET['sort_by'] ?? '') === 'personal_rating' ? 'selected' : '' ?>>Rating (High-Low)</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <!-- Quick Actions Row -->
                <div class="d-flex gap-2 align-items-center flex-wrap">
                    <div class="form-check form-switch mb-0">
                        <input class="form-check-input" type="checkbox" name="favorites" id="favorites"
                               <?= isset($_GET['favorites']) ? 'checked' : '' ?>
                               onchange="this.form.submit()">
                        <label class="form-check-label" for="favorites">
                            <i class="bi bi-heart-fill text-danger me-1"></i>Favorites Only
                        </label>
                    </div>
                    
                    <?php if (!empty($_GET['search']) || !empty($_GET['genre']) || !empty($_GET['tag']) || !empty($_GET['rating']) || isset($_GET['favorites']) || (!empty($_GET['sort_by']) && $_GET['sort_by'] !== 'date_added')): ?>
                        <div class="ms-auto">
                            <a href="<?= route_url('music') ?>" class="btn btn-outline-secondary btn-sm">
                                <i class="bi bi-x-circle me-1"></i>Clear All Filters
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <!-- Music Entries -->
        <?php if (empty($entries)): ?>
            <div class="text-center py-5">
                <i class="bi bi-music-note-list display-1 text-muted mb-3"></i>
                <h3 class="text-muted">No music entries found</h3>
                <p class="text-muted mb-4">Start building your music collection!</p>
                <a href="<?= route_url('music.add') ?>" class="btn btn-glow">
                    <i class="bi bi-plus-circle me-2"></i>Add Your First Track
                </a>
            </div>
        <?php else: ?>
            <div class="row">
                <?php foreach ($entries as $entry): ?>
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                        <div class="feature-card h-100">
                            <!-- Album Art -->
                            <?php if (!empty($entry['album_art_url'])): ?>
                                <img src="<?= e($entry['album_art_url']) ?>" class="card-img-top" 
                                     alt="<?= e($entry['title']) ?>" style="height: 200px; object-fit: cover;">
                            <?php else: ?>
                                <div class="bg-secondary d-flex align-items-center justify-content-center" 
                                     style="height: 200px;">
                                    <i class="bi bi-music-note display-4 text-muted"></i>
                                </div>
                            <?php endif; ?>
                            
                            <div class="p-3">
                                <h6 class="fw-bold mb-1"><?= e($entry['title']) ?></h6>
                                <p class="text-muted small mb-2"><?= e($entry['artist']) ?></p>
                                
                                <?php if (!empty($entry['album'])): ?>
                                    <p class="text-muted small mb-2">
                                        <i class="bi bi-disc me-1"></i><?= e($entry['album']) ?>
                                    </p>
                                <?php endif; ?>
                                
                                <!-- Tags -->
                                <?php if (!empty($entry['tags'])): ?>
                                    <div class="mb-2 d-flex flex-wrap gap-1">
                                        <?php foreach ($entry['tags'] as $tag): ?>
                                            <span class="badge" style="background-color: <?= e($tag['color']) ?>;">
                                                <?= e($tag['name']) ?>
                                            </span>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                                
                                <!-- Rating & Favorite -->
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <?php if ($entry['personal_rating']): ?>
                                        <div class="text-warning">
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                <i class="bi bi-star<?= $i <= $entry['personal_rating'] ? '-fill' : '' ?>"></i>
                                            <?php endfor; ?>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-muted small">Not rated</span>
                                    <?php endif; ?>
                                    
                                    <button class="btn btn-sm favorite-btn" data-entry-id="<?= $entry['id'] ?>"
                                            data-is-favorite="<?= $entry['is_favorite'] ? '1' : '0' ?>"
                                            title="<?= $entry['is_favorite'] ? 'Remove from favorites' : 'Add to favorites' ?>">
                                        <i class="bi bi-heart<?= $entry['is_favorite'] ? '-fill text-danger' : '' ?>"></i>
                                    </button>
                                </div>
                                
                                <!-- Actions -->
                                <div class="d-grid gap-2">
                                    <a href="<?= route_url('music') ?>/<?= $entry['id'] ?>" class="btn btn-outline-glow btn-sm">
                                        <i class="bi bi-eye me-1"></i>View Details
                                    </a>
                                    <div class="btn-group btn-group-sm">
                                        <a href="<?= route_url('music') ?>/<?= $entry['id'] ?>/edit" class="btn btn-outline-secondary">
                                            <i class="bi bi-pencil me-1"></i>Edit
                                        </a>
                                        <button class="btn btn-outline-danger delete-btn" 
                                                data-entry-id="<?= $entry['id'] ?>"
                                                data-title="<?= e($entry['title']) ?>">
                                            <i class="bi bi-trash me-1"></i>Delete
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <!-- Pagination would go here -->
        <?php endif; ?>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content bg-dark">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete "<span id="deleteTitle"></span>" from your collection?</p>
                    <p class="text-warning small">This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form id="deleteForm" method="POST" style="display: inline;">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-trash me-1"></i>Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<?php ob_start(); ?>
<script src="/assets/js/music.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips for tag dots
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
    
    // Toggle advanced filters
    const toggleBtn = document.getElementById('toggleAdvancedFilters');
    const advancedFilters = document.getElementById('advancedFilters');
    
    if (toggleBtn && advancedFilters) {
        toggleBtn.addEventListener('click', function() {
            const bsCollapse = new bootstrap.Collapse(advancedFilters, {
                toggle: true
            });
            
            // Toggle icon
            const icon = this.querySelector('i');
            advancedFilters.addEventListener('shown.bs.collapse', function() {
                icon.classList.remove('bi-funnel');
                icon.classList.add('bi-funnel-fill');
            });
            advancedFilters.addEventListener('hidden.bs.collapse', function() {
                icon.classList.remove('bi-funnel-fill');
                icon.classList.add('bi-funnel');
            });
        });
    }
    
    // Auto-submit on filter change for better UX
    const filterSelects = document.querySelectorAll('#advancedFilters select');
    filterSelects.forEach(select => {
        select.addEventListener('change', function() {
            this.form.submit();
        });
    });
});
</script>
<?php 
$additional_js = ob_get_clean();
?>