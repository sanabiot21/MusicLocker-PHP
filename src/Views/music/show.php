<!-- Music Details Page Content -->
<section class="py-5" style="margin-top: 80px;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <!-- Header -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1><i class="bi bi-music-note me-2"></i>Track Details</h1>
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

                <!-- Track Details -->
                <div class="feature-card">
                    <div class="row g-0">
                        <div class="col-md-4">
                            <?php if (!empty($entry['album_art_url'])): ?>
                                <img src="<?= e($entry['album_art_url']) ?>" class="img-fluid rounded-start h-100" 
                                     alt="<?= e($entry['title']) ?>" style="object-fit: cover; min-height: 300px;">
                            <?php else: ?>
                                <div class="bg-secondary d-flex align-items-center justify-content-center rounded-start h-100" 
                                     style="min-height: 300px;">
                                    <i class="bi bi-music-note display-1 text-muted"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-8">
                            <div class="card-body p-4">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div>
                                        <h2 class="fw-bold mb-1"><?= e($entry['title']) ?></h2>
                                        <p class="text-muted fs-5 mb-0"><?= e($entry['artist']) ?></p>
                                    </div>
                                    <button class="btn favorite-btn" data-entry-id="<?= $entry['id'] ?>"
                                            data-is-favorite="<?= $entry['is_favorite'] ? '1' : '0' ?>"
                                            title="<?= $entry['is_favorite'] ? 'Remove from favorites' : 'Add to favorites' ?>">
                                        <i class="bi bi-heart<?= $entry['is_favorite'] ? '-fill text-danger' : '' ?> fs-4"></i>
                                    </button>
                                </div>

                                <?php if (!empty($entry['album'])): ?>
                                    <p class="text-muted mb-2">
                                        <i class="bi bi-disc me-2"></i><strong>Album:</strong> <?= e($entry['album']) ?>
                                    </p>
                                <?php endif; ?>

                                <?php if (!empty($entry['genre'])): ?>
                                    <p class="text-muted mb-2">
                                        <i class="bi bi-tag me-2"></i><strong>Genre:</strong> <?= e($entry['genre']) ?>
                                    </p>
                                <?php endif; ?>

                                <?php if (!empty($entry['release_year'])): ?>
                                    <p class="text-muted mb-2">
                                        <i class="bi bi-calendar me-2"></i><strong>Year:</strong> <?= e($entry['release_year']) ?>
                                    </p>
                                <?php endif; ?>

                                <?php if (!empty($entry['duration'])): ?>
                                    <p class="text-muted mb-2">
                                        <i class="bi bi-clock me-2"></i><strong>Duration:</strong> <?= gmdate("i:s", $entry['duration']) ?>
                                    </p>
                                <?php endif; ?>

                                <!-- Personal Rating -->
                                <div class="mb-3">
                                    <strong class="text-muted me-2">
                                        <i class="bi bi-star me-1"></i>Personal Rating:
                                    </strong>
                                    <?php if ($entry['personal_rating']): ?>
                                        <span class="text-warning">
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                <i class="bi bi-star<?= $i <= $entry['personal_rating'] ? '-fill' : '' ?>"></i>
                                            <?php endfor; ?>
                                            <span class="ms-2">(<?= $entry['personal_rating'] ?>/5)</span>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-muted">Not rated</span>
                                    <?php endif; ?>
                                </div>

                                <!-- Dates -->
                                <div class="mb-3 small text-muted">
                                    <?php if (!empty($entry['date_discovered'])): ?>
                                        <p class="mb-1">
                                            <i class="bi bi-calendar-plus me-1"></i><strong>Discovered:</strong> 
                                            <?= date('M d, Y', strtotime($entry['date_discovered'])) ?>
                                        </p>
                                    <?php endif; ?>
                                    <p class="mb-0">
                                        <i class="bi bi-plus-circle me-1"></i><strong>Added:</strong> 
                                        <?= date('M d, Y g:i A', strtotime($entry['created_at'])) ?>
                                    </p>
                                </div>

                                <!-- External Links -->
                                <?php if (!empty($entry['spotify_url'])): ?>
                                    <div class="mb-3">
                                        <a href="<?= e($entry['spotify_url']) ?>" target="_blank" 
                                           class="btn btn-success btn-sm me-2">
                                            <i class="bi bi-spotify me-1"></i>Open in Spotify
                                        </a>
                                    </div>
                                <?php endif; ?>

                                <!-- Action Buttons -->
                                <div class="d-grid gap-2 d-md-flex">
                                    <a href="<?= route_url('music') ?>/<?= $entry['id'] ?>/edit" 
                                       class="btn btn-glow me-md-2">
                                        <i class="bi bi-pencil me-2"></i>Edit Track
                                    </a>
                                    <button class="btn btn-outline-danger delete-btn" 
                                            data-entry-id="<?= $entry['id'] ?>"
                                            data-title="<?= e($entry['title']) ?>">
                                        <i class="bi bi-trash me-2"></i>Delete Track
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
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
<script src="/assets/js/music-show.js"></script>
<?php 
$additional_js = ob_get_clean();
?>