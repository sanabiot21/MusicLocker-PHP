<!-- Playlist Show Page -->
<section class="py-5" style="margin-top: 80px;">
    <div class="container">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1><i class="bi bi-music-note-list me-2"></i><?= e($playlist['name']) ?></h1>
                <?php if (!empty($playlist['description'])): ?>
                    <p class="text-muted mb-0"><?= e($playlist['description']) ?></p>
                <?php endif; ?>
            </div>
            <a href="/playlists" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>Back to Playlists
            </a>
        </div>

        <!-- Playlist Info -->
        <div class="feature-card mb-4">
            <div class="row">
                <div class="col-md-8">
                    <div class="d-flex gap-4">
                        <div>
                            <div class="small text-muted">Tracks</div>
                            <div class="fs-5"><?= $playlist['track_count'] ?? 0 ?></div>
                        </div>
                        <?php if (!empty($playlist['total_duration'])): ?>
                            <div>
                                <div class="small text-muted">Duration</div>
                                <div class="fs-5"><?= gmdate("H:i:s", $playlist['total_duration']) ?></div>
                            </div>
                        <?php endif; ?>
                        <div>
                            <div class="small text-muted">Visibility</div>
                            <div>
                                <?php if ($playlist['is_public']): ?>
                                    <span class="badge bg-success"><i class="bi bi-globe"></i> Public</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary"><i class="bi bi-lock"></i> Private</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 text-md-end">
                    <a href="/playlists/<?= $playlist['id'] ?>/edit" class="btn btn-glow me-2">
                        <i class="bi bi-pencil me-1"></i>Edit
                    </a>
                    <button class="btn btn-outline-glow me-2" data-bs-toggle="modal" data-bs-target="#addTracksModal">
                        <i class="bi bi-plus-circle me-1"></i>Add Tracks
                    </button>
                    <button class="btn btn-outline-danger delete-playlist-btn" 
                            data-playlist-id="<?= $playlist['id'] ?>"
                            data-name="<?= e($playlist['name']) ?>">
                        <i class="bi bi-trash me-1"></i>Delete
                    </button>
                </div>
            </div>
        </div>

        <!-- Tracks List -->
        <?php if (empty($entries)): ?>
            <div class="feature-card text-center py-5">
                <i class="bi bi-music-note display-1 text-muted mb-3"></i>
                <h3>No Tracks Yet</h3>
                <p class="text-muted mb-4">Add tracks from your music collection to this playlist</p>
                <a href="<?= route_url('music') ?>" class="btn btn-glow">
                    <i class="bi bi-collection me-2"></i>Browse Music
                </a>
            </div>
        <?php else: ?>
            <div class="feature-card">
                <h5 class="mb-4"><i class="bi bi-music-note me-2"></i>Tracks</h5>
                <div class="list-group list-group-flush">
                    <?php foreach ($entries as $index => $entry): ?>
                        <div class="list-group-item bg-dark text-white d-flex align-items-center py-3">
                            <div class="me-3 text-muted"><?= $index + 1 ?></div>
                            
                            <?php if (!empty($entry['album_art_url'])): ?>
                                <img src="<?= e($entry['album_art_url']) ?>" class="rounded me-3" 
                                     style="width: 50px; height: 50px; object-fit: cover;" alt="<?= e($entry['title']) ?>">
                            <?php else: ?>
                                <div class="bg-secondary rounded me-3 d-flex align-items-center justify-content-center" 
                                     style="width: 50px; height: 50px;">
                                    <i class="bi bi-music-note text-muted"></i>
                                </div>
                            <?php endif; ?>
                            
                            <div class="flex-grow-1">
                                <div class="fw-bold"><a href="<?= route_url('music') ?>/<?= $entry['id'] ?>?from_playlist=<?= $playlist['id'] ?>" class="text-decoration-none text-white"><?= e($entry['title']) ?></a></div>
                                <div class="text-muted small"><?= e($entry['artist']) ?></div>
                            </div>
                            
                            <?php if (!empty($entry['duration'])): ?>
                                <div class="text-muted small me-3"><?= gmdate("i:s", $entry['duration']) ?></div>
                            <?php endif; ?>
                            
                            <button class="btn btn-outline-danger btn-sm remove-track-btn" 
                                    data-playlist-id="<?= $playlist['id'] ?>"
                                    data-entry-id="<?= $entry['entry_id'] ?>"
                                    data-title="<?= e($entry['title']) ?>"
                                    data-csrf="<?= csrf_token() ?>">
                                <i class="bi bi-x-lg"></i>
                            </button>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Add Tracks Modal -->
<div class="modal fade" id="addTracksModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content bg-dark">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-plus-circle me-2"></i>Add Tracks from Your Collection</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="input-group mb-3">
                    <span class="input-group-text bg-dark border-secondary"><i class="bi bi-search"></i></span>
                    <input type="text" class="form-control" id="addTracksSearch" placeholder="Search your collection...">
                </div>
                <div id="addTracksList" class="list-group list-group-flush">
                    <?php if (!empty($userEntries)): ?>
                        <?php foreach ($userEntries as $ue): ?>
                            <div class="list-group-item bg-dark text-white d-flex align-items-center add-track-item" data-search="<?= strtolower(e($ue['title'] . ' ' . $ue['artist'] . ' ' . ($ue['album'] ?? ''))) ?>">
                                <?php if (!empty($ue['album_art_url'])): ?>
                                    <img src="<?= e($ue['album_art_url']) ?>" class="rounded me-3" style="width: 40px; height: 40px; object-fit: cover;" alt="<?= e($ue['title']) ?>">
                                <?php else: ?>
                                    <div class="bg-secondary rounded me-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;"><i class="bi bi-music-note text-muted"></i></div>
                                <?php endif; ?>
                                <div class="flex-grow-1">
                                    <div class="fw-bold"><?= e($ue['title']) ?></div>
                                    <div class="text-muted small"><?= e($ue['artist']) ?><?= !empty($ue['album']) ? (' • ' . e($ue['album'])) : '' ?></div>
                                </div>
                                <button class="btn btn-sm btn-outline-glow btn-add-track" data-playlist-id="<?= $playlist['id'] ?>" data-entry-id="<?= $ue['id'] ?>" data-csrf="<?= csrf_token() ?>">
                                    <i class="bi bi-plus-circle me-1"></i>Add
                                </button>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="text-muted">No entries found in your collection.</div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Playlist Modal -->
<div class="modal fade" id="deletePlaylistModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content bg-dark">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete "<span id="deletePlaylistName"></span>"?</p>
                <p class="text-warning small">This will remove the playlist and all its tracks. This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deletePlaylistForm" method="POST" style="display: inline;">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-trash me-1"></i>Delete Playlist
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php ob_start(); ?>
<script src="/assets/js/playlists-show.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Client-side filter for Add Tracks modal
    const searchInput = document.getElementById('addTracksSearch');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const term = this.value.toLowerCase();
            document.querySelectorAll('#addTracksList .add-track-item').forEach(item => {
                const text = item.dataset.search;
                item.style.display = text.includes(term) ? '' : 'none';
            });
        });
    }
    // Add track buttons
    document.querySelectorAll('.btn-add-track').forEach(btn => {
        btn.addEventListener('click', async function() {
            const playlistId = this.dataset.playlistId;
            const musicEntryId = this.dataset.entryId;
            const csrfToken = this.dataset.csrf;
            this.disabled = true;
            this.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Adding';
            try {
                const response = await fetch('/playlists/add-track', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ playlist_id: playlistId, music_entry_id: musicEntryId, csrf_token: csrfToken })
                });
                const data = await response.json();
                if (data.success) {
                    this.classList.remove('btn-outline-glow');
                    this.classList.add('btn-success');
                    this.innerHTML = '<i class="bi bi-check"></i> Added';
                } else {
                    this.disabled = false;
                    this.innerHTML = '<i class="bi bi-plus-circle me-1"></i>Add';
                    alert('Failed to add: ' + (data.error || 'Unknown error'));
                }
            } catch (e) {
                this.disabled = false;
                this.innerHTML = '<i class="bi bi-plus-circle me-1"></i>Add';
                alert('Network error adding track');
            }
        });
    });
});
</script>
<?php 
$additional_js = ob_get_clean();
?>

