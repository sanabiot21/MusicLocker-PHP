<!-- Admin View: User Music Collection -->
<section class="py-5" style="margin-top: 80px;">
    <div class="container">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="feature-card">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h1 class="mb-1">
                                <i class="bi bi-music-note-list me-2" style="color: var(--accent-blue);"></i>
                                <?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?>'s Music Collection
                            </h1>
                            <p class="text-muted mb-0">
                                <?= count($entries) ?> total tracks
                            </p>
                        </div>
                        <div class="col-md-4 text-md-end">
                            <a href="/admin/users/<?= $user['id'] ?>" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-1"></i>Back to User Profile
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Music Entries -->
        <?php if (!empty($entries)): ?>
            <div class="row g-4">
                <?php foreach ($entries as $entry): ?>
                    <div class="col-lg-6">
                        <div class="feature-card h-100">
                            <div class="d-flex gap-3">
                                <!-- Album Art -->
                                <div class="flex-shrink-0">
                                    <?php if (!empty($entry['album_art_url'])): ?>
                                        <img src="<?= e($entry['album_art_url']) ?>" 
                                             alt="<?= e($entry['album_name'] ?? 'Album Art') ?>"
                                             class="rounded"
                                             style="width: 100px; height: 100px; object-fit: cover;">
                                    <?php else: ?>
                                        <div class="d-flex align-items-center justify-content-center rounded bg-dark" 
                                             style="width: 100px; height: 100px;">
                                            <i class="bi bi-music-note-beamed display-4 text-muted"></i>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <!-- Track Info -->
                                <div class="flex-grow-1 min-w-0">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div class="flex-grow-1 min-w-0">
                                            <h5 class="mb-1 text-truncate">
                                                <?= e($entry['title']) ?>
                                                <?php if ($entry['is_favorite']): ?>
                                                    <i class="bi bi-heart-fill text-danger ms-1"></i>
                                                <?php endif; ?>
                                            </h5>
                                            <p class="text-muted mb-1 text-truncate">
                                                <i class="bi bi-person me-1"></i><?= e($entry['artist']) ?>
                                            </p>
                                            <?php if (!empty($entry['album'])): ?>
                                                <p class="text-muted small mb-0 text-truncate">
                                                    <i class="bi bi-disc me-1"></i><?= e($entry['album']) ?>
                                                </p>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <!-- Rating -->
                                    <?php if (!empty($entry['personal_rating']) && $entry['personal_rating'] > 0): ?>
                                        <div class="mb-2">
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                <i class="bi bi-star<?= $i <= $entry['personal_rating'] ? '-fill text-warning' : ' text-muted' ?>"></i>
                                            <?php endfor; ?>
                                        </div>
                                    <?php endif; ?>

                                    <!-- Genre & Tags -->
                                    <div class="d-flex flex-wrap gap-1 mb-2">
                                        <?php if (!empty($entry['genre'])): ?>
                                            <span class="badge bg-secondary">
                                                <i class="bi bi-tag me-1"></i><?= e($entry['genre']) ?>
                                            </span>
                                        <?php endif; ?>
                                        <?php if (!empty($entry['tags']) && is_array($entry['tags'])): ?>
                                            <?php foreach (array_slice($entry['tags'], 0, 3) as $tag): ?>
                                                <span class="badge" style="background: rgba(138, 43, 226, 0.2); color: var(--accent-purple);">
                                                    <?= e(is_array($tag) ? $tag['name'] : $tag) ?>
                                                </span>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Personal Note -->
                                    <?php if (!empty($entry['personal_note'])): ?>
                                        <div class="mt-2 p-2 rounded" style="background: rgba(255, 255, 255, 0.05);">
                                            <small class="text-muted">
                                                <i class="bi bi-sticky me-1"></i>
                                                <?= e($entry['personal_note']) ?>
                                            </small>
                                        </div>
                                    <?php endif; ?>

                                    <!-- Metadata -->
                                    <div class="mt-2 d-flex gap-3 text-muted small">
                                        <?php if (!empty($entry['duration'])): ?>
                                            <span>
                                                <i class="bi bi-clock me-1"></i>
                                                <?= format_duration($entry['duration']) ?>
                                            </span>
                                        <?php endif; ?>
                                        <span>
                                            <i class="bi bi-calendar me-1"></i>
                                            Added <?= format_date($entry['date_added'], 'M j, Y') ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="feature-card text-center py-5">
                <i class="bi bi-music-note-list display-1 text-muted mb-3"></i>
                <h4 class="text-muted">No Music Entries</h4>
                <p class="text-muted">This user hasn't added any music to their collection yet.</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Additional CSS -->
<?php ob_start(); ?>
<style>
    .min-w-0 {
        min-width: 0;
    }
    
    .text-truncate {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    
    @media (max-width: 768px) {
        .feature-card .d-flex {
            flex-direction: column;
        }
        
        .feature-card img,
        .feature-card .bg-dark {
            width: 100% !important;
            height: 200px !important;
        }
    }
</style>
<?php 
$additional_css = ob_get_clean();
?>

