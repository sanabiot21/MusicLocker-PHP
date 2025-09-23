<?php
/**
 * Page Header Component
 * 
 * Reusable page header with stats and actions
 * Following component-based architecture
 */

function renderPageHeader(string $title, array $stats = [], array $actions = [], string $icon = 'bi-music-note-list'): string 
{
    ob_start();
    ?>
    
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                <div>
                    <h1 class="h2 mb-2">
                        <i class="bi <?= $icon ?> me-2" style="color: var(--accent-blue);"></i>
                        <?= e($title) ?>
                    </h1>
                    <?php if (!empty($stats)): ?>
                    <p class="text-muted mb-0">
                        <?php
                        $statStrings = [];
                        foreach ($stats as $key => $value) {
                            switch ($key) {
                                case 'total_entries':
                                    $statStrings[] = number_format($value) . ' tracks';
                                    break;
                                case 'favorites_count':
                                    $statStrings[] = $value . ' favorites';
                                    break;
                                case 'unique_artists':
                                    $statStrings[] = $value . ' artists';
                                    break;
                                case 'unique_genres':
                                    $statStrings[] = $value . ' genres';
                                    break;
                                case 'average_rating':
                                    if ($value > 0) {
                                        $statStrings[] = number_format($value, 1) . ' avg rating';
                                    }
                                    break;
                                default:
                                    if (is_numeric($value) && $value > 0) {
                                        $statStrings[] = number_format($value) . ' ' . str_replace('_', ' ', $key);
                                    }
                            }
                        }
                        echo implode(' • ', $statStrings);
                        ?>
                    </p>
                    <?php endif; ?>
                </div>
                
                <?php if (!empty($actions)): ?>
                <div class="mt-3 mt-md-0">
                    <?php foreach ($actions as $action): ?>
                        <?php if ($action['type'] === 'button'): ?>
                            <button type="button" class="btn <?= $action['class'] ?? 'btn-glow' ?> <?= $action['spacing'] ?? 'me-2' ?>"
                                    <?= isset($action['modal']) ? 'data-bs-toggle="modal" data-bs-target="' . $action['modal'] . '"' : '' ?>
                                    <?= isset($action['onclick']) ? 'onclick="' . $action['onclick'] . '"' : '' ?>
                                    <?= isset($action['id']) ? 'id="' . $action['id'] . '"' : '' ?>>
                                <?= isset($action['icon']) ? '<i class="bi ' . $action['icon'] . ' me-1"></i>' : '' ?>
                                <?= e($action['text']) ?>
                            </button>
                        <?php elseif ($action['type'] === 'link'): ?>
                            <a href="<?= $action['href'] ?>" class="btn <?= $action['class'] ?? 'btn-glow' ?> <?= $action['spacing'] ?? 'me-2' ?>">
                                <?= isset($action['icon']) ? '<i class="bi ' . $action['icon'] . ' me-1"></i>' : '' ?>
                                <?= e($action['text']) ?>
                            </a>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <?php
    return ob_get_clean();
}
?>