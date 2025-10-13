<!-- Edit Playlist Page -->
<section class="py-5" style="margin-top: 80px;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <!-- Header -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1><i class="bi bi-pencil me-2"></i>Edit Playlist</h1>
                    <a href="/playlists/<?= $playlist['id'] ?>" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i>Back
                    </a>
                </div>

                <!-- Edit Form -->
                <div class="feature-card">
                    <form method="POST">
                        <?= csrf_field() ?>
                        
                        <div class="mb-3">
                            <label for="name" class="form-label">Playlist Name *</label>
                            <input type="text" class="form-control" id="name" name="name" 
                                   value="<?= e($playlist['name']) ?>" required autofocus>
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3" 
                                      placeholder="What's this playlist about?"><?= e($playlist['description']) ?></textarea>
                        </div>
                        
                        
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="/playlists/<?= $playlist['id'] ?>" class="btn btn-outline-secondary me-md-2">
                                <i class="bi bi-x-circle me-1"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-glow">
                                <i class="bi bi-check-circle me-2"></i>Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

