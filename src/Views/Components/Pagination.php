<?php
/**
 * Pagination Component
 * 
 * Reusable pagination controls
 * Following component-based architecture
 */

function renderPagination(array $pagination, string $baseUrl = '', array $filters = []): string 
{
    if ($pagination['total_pages'] <= 1) {
        return '';
    }
    
    ob_start();
    
    $currentPage = $pagination['current_page'];
    $totalPages = $pagination['total_pages'];
    $showingStart = (($currentPage - 1) * $pagination['per_page']) + 1;
    $showingEnd = min($showingStart + $pagination['per_page'] - 1, $pagination['total_entries']);
    
    // Build query parameters
    $queryParams = array_filter($filters);
    $baseQueryString = $queryParams ? '&' . http_build_query($queryParams) : '';
    ?>
    
    <nav aria-label="Music collection pagination" class="mt-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="text-muted small">
                Showing <?= number_format($showingStart) ?>-<?= number_format($showingEnd) ?> 
                of <?= number_format($pagination['total_entries']) ?> entries
            </div>
            <div class="text-muted small">
                Page <?= $currentPage ?> of <?= $totalPages ?>
            </div>
        </div>
        
        <ul class="pagination justify-content-center">
            <!-- First Page -->
            <?php if ($currentPage > 1): ?>
            <li class="page-item">
                <a class="page-link" href="<?= $baseUrl ?>?page=1<?= $baseQueryString ?>" aria-label="First">
                    <i class="bi bi-chevron-double-left"></i>
                </a>
            </li>
            <?php endif; ?>
            
            <!-- Previous Page -->
            <?php if ($currentPage > 1): ?>
            <li class="page-item">
                <a class="page-link" href="<?= $baseUrl ?>?page=<?= $currentPage - 1 ?><?= $baseQueryString ?>" aria-label="Previous">
                    <i class="bi bi-chevron-left"></i>
                </a>
            </li>
            <?php endif; ?>
            
            <!-- Page Numbers -->
            <?php
            $startPage = max(1, $currentPage - 2);
            $endPage = min($totalPages, $currentPage + 2);
            
            // Adjust if we're near the beginning or end
            if ($endPage - $startPage < 4) {
                if ($startPage == 1) {
                    $endPage = min($totalPages, $startPage + 4);
                } else {
                    $startPage = max(1, $endPage - 4);
                }
            }
            ?>
            
            <?php if ($startPage > 1): ?>
            <li class="page-item disabled">
                <span class="page-link">...</span>
            </li>
            <?php endif; ?>
            
            <?php for ($page = $startPage; $page <= $endPage; $page++): ?>
            <li class="page-item <?= $page == $currentPage ? 'active' : '' ?>">
                <?php if ($page == $currentPage): ?>
                    <span class="page-link"><?= $page ?></span>
                <?php else: ?>
                    <a class="page-link" href="<?= $baseUrl ?>?page=<?= $page ?><?= $baseQueryString ?>">
                        <?= $page ?>
                    </a>
                <?php endif; ?>
            </li>
            <?php endfor; ?>
            
            <?php if ($endPage < $totalPages): ?>
            <li class="page-item disabled">
                <span class="page-link">...</span>
            </li>
            <?php endif; ?>
            
            <!-- Next Page -->
            <?php if ($currentPage < $totalPages): ?>
            <li class="page-item">
                <a class="page-link" href="<?= $baseUrl ?>?page=<?= $currentPage + 1 ?><?= $baseQueryString ?>" aria-label="Next">
                    <i class="bi bi-chevron-right"></i>
                </a>
            </li>
            <?php endif; ?>
            
            <!-- Last Page -->
            <?php if ($currentPage < $totalPages): ?>
            <li class="page-item">
                <a class="page-link" href="<?= $baseUrl ?>?page=<?= $totalPages ?><?= $baseQueryString ?>" aria-label="Last">
                    <i class="bi bi-chevron-double-right"></i>
                </a>
            </li>
            <?php endif; ?>
        </ul>
        
        <!-- Page Size Options -->
        <div class="d-flex justify-content-center mt-3">
            <div class="dropdown">
                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" 
                        data-bs-toggle="dropdown" aria-expanded="false">
                    <?= $pagination['per_page'] ?> per page
                </button>
                <ul class="dropdown-menu">
                    <?php foreach ([10, 20, 50, 100] as $perPage): ?>
                    <li>
                        <a class="dropdown-item <?= $pagination['per_page'] == $perPage ? 'active' : '' ?>" 
                           href="<?= $baseUrl ?>?page=1&limit=<?= $perPage ?><?= $baseQueryString ?>">
                            <?= $perPage ?> per page
                        </a>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </nav>
    
    <?php
    return ob_get_clean();
}
?>