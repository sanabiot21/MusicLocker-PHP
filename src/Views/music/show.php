<!-- Music Details Page Content -->
<section class="py-5" style="margin-top: 80px;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10" style="position: relative;">
                <!-- Back Button (top right outside card) -->
                <div class="d-flex justify-content-end mb-3">
                    <?php $fromPlaylist = $_GET['from_playlist'] ?? null; ?>
                    <?php if ($fromPlaylist): ?>
                        <a href="/playlists/<?= (int)$fromPlaylist ?>" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-arrow-left me-1"></i>Back to Playlist
                        </a>
                    <?php else: ?>
                        <a href="<?= route_url('music') ?>" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-arrow-left me-1"></i>Back to Collection
                    </a>
                    <?php endif; ?>
                </div>
                <!-- Hero Header Card -->
                <div class="feature-card mb-4" style="overflow: hidden;">
                    <div class="row g-0">
                        <!-- Album Art -->
                        <div class="col-md-4">
                            <?php if (!empty($entry['album_art_url'])): ?>
                                <div class="album-art-container" style="position: relative;">
                                    <img src="<?= e($entry['album_art_url']) ?>" 
                                         class="img-fluid w-100" 
                                         alt="<?= e($entry['title']) ?>"
                                         style="height: 400px; object-fit: cover; border-radius: 8px; border: 2px solid var(--accent-blue); box-shadow: 0 0 20px rgba(0, 212, 255, 0.3);">
                                </div>
                            <?php else: ?>
                                <div class="bg-secondary d-flex align-items-center justify-content-center" 
                                     style="height: 400px; border-radius: 8px; border: 2px solid rgba(255,255,255,0.1);">
                                    <div class="text-center">
                                        <i class="bi bi-vinyl display-1 text-muted mb-3"></i>
                                        <p class="text-muted">No Album Art</p>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Track Info & Actions -->
                        <div class="col-md-8 p-4">
                            <!-- Title & Favorite -->
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div class="flex-grow-1 me-3">
                                    <h1 class="fw-bold mb-2" style="font-size: 2rem; line-height: 1.2;">
                                        <?= e($entry['title']) ?>
                                    </h1>
                                    <h4 class="text-muted mb-0"><?= e($entry['artist']) ?></h4>
                                    </div>
                                <button class="btn btn-lg favorite-btn pulse-on-hover" 
                                        data-entry-id="<?= $entry['id'] ?>"
                                            data-is-favorite="<?= $entry['is_favorite'] ? '1' : '0' ?>"
                                        title="<?= $entry['is_favorite'] ? 'Remove from favorites' : 'Add to favorites' ?>"
                                        style="min-width: 60px; border: none; background: transparent;">
                                    <i class="bi bi-heart<?= $entry['is_favorite'] ? '-fill text-danger' : '' ?>" 
                                       style="font-size: 2.5rem; <?= $entry['is_favorite'] ? 'text-shadow: 0 0 15px #ff0040;' : '' ?>"></i>
                                    </button>
                                </div>

                            <!-- Metadata Pills -->
                            <div class="d-flex flex-wrap gap-2 mb-4">
                                <?php if (!empty($entry['genre'])): ?>
                                    <span class="badge bg-gradient-primary px-3 py-2" style="font-size: 0.9rem;">
                                        <i class="bi bi-tag me-1"></i><?= e($entry['genre']) ?>
                                    </span>
                                <?php endif; ?>

                                <?php if (!empty($entry['release_year'])): ?>
                                    <span class="badge bg-dark px-3 py-2" style="font-size: 0.9rem; border: 1px solid var(--accent-blue);">
                                        <i class="bi bi-calendar me-1"></i><?= e($entry['release_year']) ?>
                                    </span>
                                <?php endif; ?>

                                <?php if (!empty($entry['duration'])): ?>
                                    <span class="badge bg-dark px-3 py-2" style="font-size: 0.9rem; border: 1px solid var(--accent-purple);">
                                        <i class="bi bi-clock me-1"></i><?= gmdate("i:s", $entry['duration']) ?>
                                    </span>
                                <?php endif; ?>
                                
                                <?php if (!empty($entry['album'])): ?>
                                    <span class="badge bg-dark px-3 py-2" style="font-size: 0.9rem; border: 1px solid rgba(255,255,255,0.2);">
                                        <i class="bi bi-disc me-1"></i><?= e($entry['album']) ?>
                                    </span>
                                <?php endif; ?>
                            </div>

                                <!-- Personal Rating -->
                            <div class="mb-4">
                                <div class="small text-muted mb-2">Personal Rating</div>
                                <div class="rating-display">
                                    <?php if ($entry['personal_rating']): ?>
                                        <div class="d-flex align-items-center">
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                <i class="bi bi-star<?= $i <= $entry['personal_rating'] ? '-fill' : '' ?> me-1" 
                                                   style="font-size: 1.5rem; color: <?= $i <= $entry['personal_rating'] ? '#FFD700' : '#666' ?>; text-shadow: <?= $i <= $entry['personal_rating'] ? '0 0 10px rgba(255, 215, 0, 0.5)' : 'none' ?>;"></i>
                                            <?php endfor; ?>
                                            <span class="ms-2 text-muted"><?= $entry['personal_rating'] ?>/5</span>
                                        </div>
                                    <?php else: ?>
                                        <div class="d-flex align-items-center">
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                <i class="bi bi-star me-1" style="font-size: 1.5rem; color: #444;"></i>
                                            <?php endfor; ?>
                                            <span class="ms-2 text-muted fst-italic">Not rated</span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                </div>

                            <!-- Tags -->
                            <?php if (!empty($tags)): ?>
                                <div class="mb-4">
                                    <div class="small text-muted mb-2">Tags</div>
                                    <div class="d-flex flex-wrap gap-2">
                                        <?php foreach ($tags as $tag): ?>
                                            <span class="badge px-3 py-2" 
                                                  style="background-color: <?= e($tag['color']) ?>; font-size: 0.9rem; border-radius: 20px;">
                                                <?= e($tag['name']) ?>
                                            </span>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <!-- Action Buttons -->
                            <div class="d-flex flex-wrap gap-2 mb-3">
                                <a href="<?= route_url('music') ?>/<?= $entry['id'] ?>/edit" 
                                   class="btn btn-glow">
                                    <i class="bi bi-pencil me-1"></i>Edit
                                </a>
                                
                                <?php if (!empty($entry['spotify_url'])): ?>
                                    <a href="<?= e($entry['spotify_url']) ?>" 
                                       target="_blank" 
                                       rel="noopener noreferrer"
                                       class="btn btn-success">
                                            <i class="bi bi-spotify me-1"></i>Open in Spotify
                                        </a>
                                <?php endif; ?>
                                
                                <button class="btn btn-outline-secondary copy-link-btn" 
                                        data-url="<?= e($_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']) ?>"
                                        title="Copy link to this track">
                                    <i class="bi bi-link-45deg me-1"></i>Copy Link
                                </button>
                                
                                <button class="btn btn-outline-danger delete-btn ms-auto" 
                                        data-entry-id="<?= $entry['id'] ?>"
                                        data-title="<?= e($entry['title']) ?>">
                                    <i class="bi bi-trash me-1"></i>Delete
                                </button>
                            </div>

                            <!-- Dates -->
                            <div class="small text-muted">
                                <?php if (!empty($entry['date_discovered'])): ?>
                                    <div class="mb-1">
                                        <i class="bi bi-calendar-plus me-1"></i>
                                        Discovered <?= date('M d, Y', strtotime($entry['date_discovered'])) ?>
                                    </div>
                                <?php endif; ?>
                                <div>
                                    <i class="bi bi-plus-circle me-1"></i>
                                    Added <?= date('M d, Y g:i A', strtotime($entry['created_at'])) ?>
                                </div>
                            </div>

                            <!-- Personal Notes (inline in the same card) -->
                            <?php if (!empty($note) && (!empty($note['note_text']) || !empty($note['mood']) || !empty($note['memory_context']) || !empty($note['listening_context']))): ?>
                                    <hr class="my-4">
                                <h5 class="mb-3"><i class="bi bi-journal-text me-2"></i>Personal Notes</h5>
                                    
                                    <?php if (!empty($note['note_text'])): ?>
                                    <div class="note-content p-3 mb-3" 
                                         style="background: rgba(0, 0, 0, 0.3); border-left: 3px solid var(--accent-blue); border-radius: 4px;">
                                        <p class="mb-0" style="white-space: pre-wrap;"><?= nl2br(e($note['note_text'])) ?></p>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="row g-3 mb-2">
                                    <?php if (!empty($note['mood'])): ?>
                                        <div class="col-md-4">
                                            <div class="small text-muted mb-1">Mood</div>
                                            <span class="badge bg-gradient-primary px-3 py-2">
                                                <i class="bi bi-emoji-smile me-1"></i><?= e($note['mood']) ?>
                                            </span>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <?php if (!empty($note['memory_context'])): ?>
                                            <div class="col-md-4">
                                            <div class="small text-muted mb-1">Memory</div>
                                            <div class="text-light"><?= e($note['memory_context']) ?></div>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <?php if (!empty($note['listening_context'])): ?>
                                            <div class="col-md-4">
                                            <div class="small text-muted mb-1">Listening Context</div>
                                            <div class="text-light"><?= e($note['listening_context']) ?></div>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    
                                <div class="small text-muted">
                                    <i class="bi bi-clock me-1"></i>Last updated <?= date('M d, Y', strtotime($note['updated_at'])) ?>
                                </div>
                            <?php endif; ?>
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
                <div class="modal-header border-bottom border-secondary">
                    <h5 class="modal-title">Confirm Delete</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete "<strong id="deleteTitle"></strong>" from your collection?</p>
                    <p class="text-warning small mb-0">
                        <i class="bi bi-exclamation-triangle me-1"></i>This action cannot be undone.
                    </p>
                </div>
                <div class="modal-footer border-top border-secondary">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form id="deleteForm" method="POST" style="display: inline;">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-trash me-1"></i>Delete Track
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<?php ob_start(); ?>
<script src="/assets/js/music-show.js"></script>
<script>
// Copy link functionality
document.addEventListener('DOMContentLoaded', function() {
    const copyBtn = document.querySelector('.copy-link-btn');
    if (copyBtn) {
        copyBtn.addEventListener('click', function() {
            const url = this.dataset.url;
            
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(url).then(() => {
                    const originalHtml = this.innerHTML;
                    this.innerHTML = '<i class="bi bi-check me-1"></i>Copied!';
                    this.classList.remove('btn-outline-secondary');
                    this.classList.add('btn-success');
                    
                    setTimeout(() => {
                        this.innerHTML = originalHtml;
                        this.classList.remove('btn-success');
                        this.classList.add('btn-outline-secondary');
                    }, 2000);
                    
                    if (window.MusicLocker && typeof window.MusicLocker.showToast === 'function') {
                        window.MusicLocker.showToast('Link copied to clipboard!', 'success');
                    }
                }).catch(err => {
                    console.error('Copy failed:', err);
                    if (window.MusicLocker && typeof window.MusicLocker.showToast === 'function') {
                        window.MusicLocker.showToast('Failed to copy link', 'error');
                    }
                });
            }
        });
    }
});
</script>
<?php 
$additional_js = ob_get_clean();
?>
