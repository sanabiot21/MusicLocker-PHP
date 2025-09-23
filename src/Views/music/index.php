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
            </div>
        </div>

        <!-- Filters -->
        <div class="feature-card mb-4">
            <form method="GET" action="<?= route_url('music') ?>" class="row g-3">
                <div class="col-md-4">
                    <input type="text" class="form-control" name="search" placeholder="Search music..." 
                           value="<?= e($_GET['search'] ?? '') ?>">
                </div>
                <div class="col-md-2">
                    <select class="form-select" name="genre">
                        <option value="">All Genres</option>
                        <?php foreach ($genres as $genre): ?>
                            <option value="<?= e($genre) ?>" <?= ($_GET['genre'] ?? '') === $genre ? 'selected' : '' ?>>
                                <?= e($genre) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select" name="rating">
                        <option value="">All Ratings</option>
                        <option value="5" <?= ($_GET['rating'] ?? '') === '5' ? 'selected' : '' ?>>5 Stars</option>
                        <option value="4" <?= ($_GET['rating'] ?? '') === '4' ? 'selected' : '' ?>>4+ Stars</option>
                        <option value="3" <?= ($_GET['rating'] ?? '') === '3' ? 'selected' : '' ?>>3+ Stars</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select" name="sort_by">
                        <option value="created_at" <?= ($_GET['sort_by'] ?? 'created_at') === 'created_at' ? 'selected' : '' ?>>Recently Added</option>
                        <option value="title" <?= ($_GET['sort_by'] ?? '') === 'title' ? 'selected' : '' ?>>Title</option>
                        <option value="artist" <?= ($_GET['sort_by'] ?? '') === 'artist' ? 'selected' : '' ?>>Artist</option>
                        <option value="personal_rating" <?= ($_GET['sort_by'] ?? '') === 'personal_rating' ? 'selected' : '' ?>>Rating</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <div class="form-check form-switch mt-2">
                        <input class="form-check-input" type="checkbox" name="favorites" id="favorites"
                               <?= isset($_GET['favorites']) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="favorites">Favorites Only</label>
                    </div>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-outline-glow me-2">
                        <i class="bi bi-search me-1"></i>Filter
                    </button>
                    <a href="<?= route_url('music') ?>" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-counterclockwise me-1"></i>Clear
                    </a>
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

    </div>
</section>

<?php ob_start(); ?>
<script src="/assets/js/music.js"></script>
<?php 
$additional_js = ob_get_clean();
?>