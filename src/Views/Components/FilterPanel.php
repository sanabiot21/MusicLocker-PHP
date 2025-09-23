<?php
/**
 * Filter Panel Component
 * 
 * Reusable filter sidebar for music collection
 * Following component-based architecture
 */

function renderFilterPanel(array $filters, array $popularGenres, array $userTags): string 
{
    ob_start();
    ?>
    <div class="card border-0 bg-dark-subtle">
        <div class="card-body">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h5 class="card-title mb-0">
                    <i class="bi bi-funnel me-2" style="color: var(--accent-blue);"></i>
                    Filters
                </h5>
                <button class="btn btn-sm btn-outline-secondary" onclick="clearFilters()">
                    <i class="bi bi-x-circle me-1"></i>Clear
                </button>
            </div>

            <!-- Search -->
            <div class="mb-3">
                <label class="form-label small">Search Music</label>
                <input type="text" class="form-control form-control-sm" id="searchInput" 
                       placeholder="Search titles, artists, albums..." 
                       value="<?= e($filters['search']) ?>">
            </div>

            <!-- Genre Filter -->
            <?php if (!empty($popularGenres)): ?>
            <div class="mb-3">
                <label class="form-label small">Genre</label>
                <select class="form-select form-select-sm" id="genreFilter">
                    <option value="">All Genres</option>
                    <?php foreach ($popularGenres as $genre): ?>
                        <option value="<?= e($genre['genre']) ?>" 
                                <?= $filters['genre'] === $genre['genre'] ? 'selected' : '' ?>>
                            <?= e($genre['genre']) ?> (<?= $genre['count'] ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php endif; ?>

            <!-- Rating Filter -->
            <div class="mb-3">
                <label class="form-label small">Minimum Rating</label>
                <select class="form-select form-select-sm" id="ratingFilter">
                    <option value="">Any Rating</option>
                    <option value="5" <?= $filters['rating'] === '5' ? 'selected' : '' ?>>⭐⭐⭐⭐⭐ Only</option>
                    <option value="4" <?= $filters['rating'] === '4' ? 'selected' : '' ?>>⭐⭐⭐⭐ & Up</option>
                    <option value="3" <?= $filters['rating'] === '3' ? 'selected' : '' ?>>⭐⭐⭐ & Up</option>
                    <option value="2" <?= $filters['rating'] === '2' ? 'selected' : '' ?>>⭐⭐ & Up</option>
                </select>
            </div>

            <!-- Favorites Filter -->
            <div class="mb-3">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="favoritesFilter" 
                           <?= $filters['favorites'] ? 'checked' : '' ?>>
                    <label class="form-check-label small" for="favoritesFilter">
                        <i class="bi bi-heart-fill me-1" style="color: var(--accent-purple);"></i>
                        Favorites Only
                    </label>
                </div>
            </div>

            <!-- Tags Filter -->
            <?php if (!empty($userTags)): ?>
            <div class="mb-3">
                <label class="form-label small">Tags</label>
                <div class="tag-filter-container">
                    <?php foreach (array_slice($userTags, 0, 10) as $tag): ?>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" 
                                   id="tag_<?= $tag['id'] ?>" value="<?= $tag['id'] ?>" 
                                   name="tags[]" <?= in_array($tag['id'], $filters['selected_tags'] ?? []) ? 'checked' : '' ?>>
                            <label class="form-check-label small" for="tag_<?= $tag['id'] ?>">
                                <span class="badge rounded-pill" style="background-color: <?= e($tag['color']) ?>;">
                                    <?= e($tag['name']) ?>
                                </span>
                            </label>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Sort Options -->
            <div class="mb-3">
                <label class="form-label small">Sort By</label>
                <select class="form-select form-select-sm" id="sortByFilter">
                    <option value="date_added" <?= $filters['sort_by'] === 'date_added' ? 'selected' : '' ?>>Recently Added</option>
                    <option value="title" <?= $filters['sort_by'] === 'title' ? 'selected' : '' ?>>Title A-Z</option>
                    <option value="artist" <?= $filters['sort_by'] === 'artist' ? 'selected' : '' ?>>Artist A-Z</option>
                    <option value="rating" <?= $filters['sort_by'] === 'rating' ? 'selected' : '' ?>>Rating</option>
                    <option value="play_count" <?= $filters['sort_by'] === 'play_count' ? 'selected' : '' ?>>Most Played</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label small">Order</label>
                <select class="form-select form-select-sm" id="sortOrderFilter">
                    <option value="DESC" <?= $filters['sort_order'] === 'DESC' ? 'selected' : '' ?>>Descending</option>
                    <option value="ASC" <?= $filters['sort_order'] === 'ASC' ? 'selected' : '' ?>>Ascending</option>
                </select>
            </div>
        </div>
    </div>
    <?php
    return ob_get_clean();
}
?>