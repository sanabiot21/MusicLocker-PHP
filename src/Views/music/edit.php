<!-- Edit Music Page Content -->
<section class="py-5" style="margin-top: 80px;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <!-- Header -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1><i class="bi bi-pencil me-2"></i>Edit Track</h1>
                    <div>
                        <a href="<?= route_url('music') ?>/<?= $entry['id'] ?>" class="btn btn-outline-secondary me-2">
                            <i class="bi bi-eye me-1"></i>View Details
                        </a>
                        <a href="<?= route_url('music') ?>" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-1"></i>Back to Collection
                        </a>
                    </div>
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

                <!-- Current Track Preview -->
                <div class="feature-card mb-4">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="bi bi-info-circle me-2"></i>Current Track Information</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3 align-items-center">
                            <div class="col-auto">
                                <?php if (!empty($entry['album_art_url'])): ?>
                                    <img src="<?= e($entry['album_art_url']) ?>" class="rounded" 
                                         alt="<?= e($entry['title']) ?>" style="width: 80px; height: 80px; object-fit: cover;">
                                <?php else: ?>
                                    <div class="bg-secondary d-flex align-items-center justify-content-center rounded" 
                                         style="width: 80px; height: 80px;">
                                        <i class="bi bi-music-note fs-3 text-muted"></i>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="col">
                                <h6 class="mb-1"><?= e($entry['title']) ?></h6>
                                <p class="text-muted small mb-1"><?= e($entry['artist']) ?></p>
                                <?php if (!empty($entry['album'])): ?>
                                    <p class="text-muted small mb-0">
                                        <i class="bi bi-disc me-1"></i><?= e($entry['album']) ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                            <div class="col-auto">
                                <?php if ($entry['personal_rating']): ?>
                                    <div class="text-warning">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <i class="bi bi-star<?= $i <= $entry['personal_rating'] ? '-fill' : '' ?>"></i>
                                        <?php endfor; ?>
                                    </div>
                                <?php endif; ?>
                                <?php if ($entry['is_favorite']): ?>
                                    <i class="bi bi-heart-fill text-danger ms-2"></i>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Edit Form -->
                <div class="feature-card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bi bi-pencil me-2"></i>Edit Track Details</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <?= csrf_field() ?>
                            
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="title" class="form-label">Track Title *</label>
                                    <input type="text" class="form-control" id="title" name="title" 
                                           value="<?= e($entry['title']) ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="artist" class="form-label">Artist *</label>
                                    <input type="text" class="form-control" id="artist" name="artist" 
                                           value="<?= e($entry['artist']) ?>" required>
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="album" class="form-label">Album</label>
                                    <input type="text" class="form-control" id="album" name="album" 
                                           value="<?= e($entry['album']) ?>">
                                </div>
                                <div class="col-md-3">
                                    <label for="genre" class="form-label">Genre</label>
                                    <input type="text" class="form-control" id="genre" name="genre" 
                                           value="<?= e($entry['genre']) ?>">
                                </div>
                                <div class="col-md-3">
                                    <label for="release_year" class="form-label">Year</label>
                                    <input type="number" class="form-control" id="release_year" name="release_year" 
                                           value="<?= e($entry['release_year']) ?>" min="1900" max="<?= date('Y') ?>">
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="personal_rating" class="form-label">Personal Rating</label>
                                    <select class="form-select" id="personal_rating" name="personal_rating">
                                        <option value="" <?= !$entry['personal_rating'] ? 'selected' : '' ?>>Not Rated</option>
                                        <option value="1" <?= $entry['personal_rating'] == 1 ? 'selected' : '' ?>>1 Star</option>
                                        <option value="2" <?= $entry['personal_rating'] == 2 ? 'selected' : '' ?>>2 Stars</option>
                                        <option value="3" <?= $entry['personal_rating'] == 3 ? 'selected' : '' ?>>3 Stars</option>
                                        <option value="4" <?= $entry['personal_rating'] == 4 ? 'selected' : '' ?>>4 Stars</option>
                                        <option value="5" <?= $entry['personal_rating'] == 5 ? 'selected' : '' ?>>5 Stars</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check form-switch mt-4">
                                        <input class="form-check-input" type="checkbox" id="is_favorite" name="is_favorite"
                                               <?= $entry['is_favorite'] ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="is_favorite">
                                            <i class="bi bi-heart me-1"></i>Favorite Track
                                        </label>
                                    </div>
                                </div>
                                
                                <?php if (!empty($entry['spotify_url'])): ?>
                                    <div class="col-12">
                                        <div class="alert alert-info d-flex align-items-center">
                                            <i class="bi bi-spotify me-2"></i>
                                            <div class="flex-grow-1">
                                                This track is linked to Spotify. Some metadata cannot be edited.
                                            </div>
                                            <a href="<?= e($entry['spotify_url']) ?>" target="_blank" class="btn btn-outline-info btn-sm">
                                                <i class="bi bi-box-arrow-up-right me-1"></i>View on Spotify
                                            </a>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="col-12">
                                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                        <a href="<?= route_url('music') ?>/<?= $entry['id'] ?>" class="btn btn-outline-secondary me-md-2">
                                            <i class="bi bi-x-circle me-1"></i>Cancel
                                        </a>
                                        <button type="submit" class="btn btn-glow">
                                            <i class="bi bi-check-circle me-2"></i>Save Changes
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
</section>